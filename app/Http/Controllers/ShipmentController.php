<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function deliver(Shipment $shipment): RedirectResponse
    {
        $this->authorize('deliver', $shipment);

        $shipment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $shipment->aidRequest->update(['status' => 'delivered']);

        return redirect()->route('dashboard')->with('success', __('Shipment has been confirmed as received in the field.'));
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
}
