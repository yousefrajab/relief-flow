<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\User;
use App\Notifications\ShipmentDeliveredNotification;
use App\Services\AIService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        $shipment->load(['aidRequest.requestItems.item', 'aidRequest.user', 'warehouse']);

        return view('shipments.show', compact('shipment'));
    }

    public function deliver(Request $request, Shipment $shipment, AIService $aiService): RedirectResponse
    {
        $this->authorize('deliver', $shipment);

        $request->validate([
            'delivery_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $update = [
            'status' => 'delivered',
            'delivered_at' => now(),
        ];

        if ($request->hasFile('delivery_photo')) {
            $shipment->load('aidRequest.requestItems.item');
            $expectedItems = $shipment->aidRequest->requestItems->pluck('item.name')->all();

            $result = $aiService->verifyDeliveryPhoto($request->file('delivery_photo'), $expectedItems);

            $update['delivery_photo_path'] = $request->file('delivery_photo')->store('deliveries', 'public');
            $update['ai_verification_status'] = $result['status'] ?? 'needs_review';
            $update['ai_verification_notes'] = $result['notes'] ?? null;
        }

        $shipment->update($update);
        $shipment->aidRequest->update(['status' => 'delivered']);

        $staff = User::whereIn('role', ['admin', 'depot_manager'])->where('status', 'active')->get();
        if ($staff->isNotEmpty()) {
            Notification::send($staff, new ShipmentDeliveredNotification($shipment));
        }

        return redirect()->route('shipments.show', $shipment)->with('success', __('Shipment has been confirmed as received in the field.'));
    }

    public function print(Shipment $shipment, QrCodeService $qrCodeService): View
    {
        $this->authorize('view', $shipment);

        $shipment->load(['aidRequest.requestItems.item', 'aidRequest.user', 'warehouse']);

        return view('shipments.print', [
            'shipment' => $shipment,
            'qrCode' => $qrCodeService->dataUri($shipment->qr_code_token),
        ]);
    }

    public function track(string $token, QrCodeService $qrCodeService): View
    {
        $shipment = Shipment::with(['aidRequest.requestItems.item', 'warehouse'])
            ->where('qr_code_token', $token)
            ->firstOrFail();

        return view('tracking.show', [
            'shipment' => $shipment,
            'qrCode' => $qrCodeService->dataUri($shipment->qr_code_token, 140),
        ]);
    }
}
