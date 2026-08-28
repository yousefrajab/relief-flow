<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShipmentDispatchedNotification extends Notification
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'webpush'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('Your shipment is on the way'),
            __('A shipment has been dispatched from :warehouse for your request. Driver: :driver (:phone). Tracking token: :token', [
                'warehouse' => $this->shipment->warehouse->name,
                'driver' => $this->shipment->driver_name,
                'phone' => $this->shipment->driver_phone,
                'token' => $this->shipment->qr_code_token,
            ]),
            route('shipments.show', $this->shipment),
            __('Track shipment')
        ))->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Shipment :token has been dispatched to you.', ['token' => $this->shipment->qr_code_token]),
            'url' => route('shipments.show', $this->shipment),
        ];
    }
}
