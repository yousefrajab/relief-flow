<?php

namespace App\Http\Controllers;

use App\Models\AidRequest;
use App\Models\AidRequestActivity;
use App\Models\AidRequestItem;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\AidRequestRejectedNotification;
use App\Notifications\AidRequestSubmittedNotification;
use App\Notifications\ShipmentDispatchedNotification;
use App\Services\AIService;
use App\Services\LogisticsService;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AidRequestController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $query = AidRequest::with(['requestItems.item', 'user', 'shipment']);

        if ($user->role === 'coordinator') {
            $query->where('user_id', $user->id);
        }

        $aidRequests = $query->orderBy('id', 'desc')->paginate(15);

        return view('aid-requests.index', compact('aidRequests'));
    }

    public function create(): View
    {
        $this->authorize('create', AidRequest::class);

        $items = Item::orderBy('name')->get();

        return view('aid-requests.create', compact('items'));
    }

    public function store(Request $request, AIService $aiService): RedirectResponse
    {
        $this->authorize('create', AidRequest::class);

        $request->validate([
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $priority = $aiService->classifyPriority($request->location, $request->notes);

        $aidRequest = DB::transaction(function () use ($request, $priority) {
            $aidRequest = AidRequest::create([
                'user_id' => Auth::id(),
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'notes' => $request->notes,
                'status' => 'pending',
                'priority' => $priority,
            ]);

            foreach ($request->items as $item) {
                AidRequestItem::create([
                    'aid_request_id' => $aidRequest->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $aidRequest;
        });

        AidRequestActivity::create([
            'aid_request_id' => $aidRequest->id,
            'user_id' => Auth::id(),
            'action' => 'submitted',
        ]);

        $staff = User::whereIn('role', ['admin', 'depot_manager'])->where('status', 'active')->get();
        if ($staff->isNotEmpty()) {
            Notification::send($staff, new AidRequestSubmittedNotification($aidRequest));
        }

        return redirect()->route('aid-requests.show', $aidRequest)->with('success', __('Field aid request has been submitted and is pending review.'));
    }

    public function show(AidRequest $aidRequest, LogisticsService $logisticsService): View
    {
        $this->authorize('view', $aidRequest);

        $aidRequest->load(['requestItems.item', 'user', 'shipment.warehouse', 'activities.user']);

        $matches = null;
        if ($aidRequest->status === 'pending') {
            $matches = $logisticsService->rankWarehousesFor($aidRequest);
        }

        return view('aid-requests.show', compact('aidRequest', 'matches'));
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

        AidRequestActivity::create([
            'aid_request_id' => $aidRequest->id,
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'notes' => $request->rejection_reason,
        ]);

        $aidRequest->user->notify(new AidRequestRejectedNotification($aidRequest));

        return redirect()->route('aid-requests.show', $aidRequest)->with('success', __('Aid request has been rejected.'));
    }

    public function dispatch(Request $request, AidRequest $aidRequest, NotificationService $notificationService): RedirectResponse
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

                return back()->withErrors([
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

        $shipment->setRelation('warehouse', $warehouse);

        AidRequestActivity::create([
            'aid_request_id' => $aidRequest->id,
            'user_id' => Auth::id(),
            'action' => 'dispatched',
            'notes' => __('From :warehouse, driver :driver', ['warehouse' => $warehouse->name, 'driver' => $request->driver_name]),
        ]);

        $aidRequest->user->notify(new ShipmentDispatchedNotification($shipment));

        $trackingUrl = route('tracking.show', $shipment->qr_code_token);
        $driverMessage = __('New ReliefFlow delivery task: pick up from :warehouse for :location. Track: :url', [
            'warehouse' => $warehouse->name,
            'location' => $aidRequest->location,
            'url' => $trackingUrl,
        ]);
        $notificationService->sendSMS($request->driver_phone, $driverMessage);
        $notificationService->sendWhatsApp($request->driver_phone, $driverMessage);

        return redirect()->route('aid-requests.show', $aidRequest)->with('success', __('Shipment dispatched successfully. Tracking token: :token', ['token' => $shipment->qr_code_token]));
    }
}
