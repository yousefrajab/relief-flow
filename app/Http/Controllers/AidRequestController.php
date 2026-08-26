<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\AidRequestItem;
use App\Models\Inventory;
use App\Models\Shipment;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AidRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AidRequest::class);

        $request->validate([
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request) {
            $aidRequest = AidRequest::create([
                'user_id' => Auth::id(),
                'location' => $request->location,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                AidRequestItem::create([
                    'aid_request_id' => $aidRequest->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('dashboard')->with('success', __('Field aid request has been submitted and is pending review.'));
    }

    public function reject(Request $request, AidRequest $aidRequest): RedirectResponse
    {
        $this->authorize('reject', $aidRequest);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $aidRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('dashboard')->with('success', __('Aid request has been rejected.'));
    }

    public function dispatch(Request $request, AidRequest $aidRequest): RedirectResponse
    {
        $this->authorize('dispatch', $aidRequest);

        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'driver_name' => ['required', 'string', 'min:3', 'max:255'],
            'driver_phone' => ['required', 'string', 'min:7', 'max:20'],
        ]);

        $aidRequest->load('requestItems.item');
        $warehouse = Warehouse::findOrFail($request->warehouse_id);

        foreach ($aidRequest->requestItems as $requestItem) {
            $inventory = Inventory::where('warehouse_id', $warehouse->id)
                ->where('item_id', $requestItem->item_id)
                ->first();

            if (! $inventory || $inventory->quantity < $requestItem->quantity) {
                $available = $inventory?->quantity ?? 0;

                return redirect()->route('dashboard')->withErrors([
                    'warehouse_id' => __('Insufficient stock in :warehouse for :item (requested :requested, available :available).', [
                        'warehouse' => $warehouse->name,
                        'item' => $requestItem->item->name,
                        'requested' => number_format($requestItem->quantity),
                        'available' => number_format($available),
                    ]),
                ]);
            }
        }

        $shipment = DB::transaction(function () use ($aidRequest, $warehouse, $request) {
            foreach ($aidRequest->requestItems as $requestItem) {
                Inventory::where('warehouse_id', $warehouse->id)
                    ->where('item_id', $requestItem->item_id)
                    ->decrement('quantity', $requestItem->quantity);
            }

            $aidRequest->update(['status' => 'dispatched']);

            return Shipment::create([
                'aid_request_id' => $aidRequest->id,
                'warehouse_id' => $warehouse->id,
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'status' => 'dispatched',
                'qr_code_token' => 'RF-'.strtoupper(bin2hex(random_bytes(4))),
            ]);
        });

        return redirect()->route('dashboard')->with('success', __('Shipment dispatched successfully. Tracking token: :token', ['token' => $shipment->qr_code_token]));
    }
}
