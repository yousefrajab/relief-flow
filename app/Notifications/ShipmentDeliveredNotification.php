<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShipmentDeliveredNotification extends Notification
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Shipment :token has been confirmed as delivered.', ['token' => $this->shipment->qr_code_token]),
            'url' => route('shipments.show', $this->shipment),
        ];
    }
}
