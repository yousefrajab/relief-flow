<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('New delivery assigned to you'),
            __('Pick up from :warehouse and deliver to :location. Tracking token: :token', [
                'warehouse' => $this->shipment->warehouse->name,
                'location' => $this->shipment->aidRequest->location,
                'token' => $this->shipment->qr_code_token,
            ]),
            route('shipments.show', $this->shipment),
            __('View delivery')
        ))->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('New delivery assigned to you: pick up from :warehouse for :location.', [
                'warehouse' => $this->shipment->warehouse->name,
                'location' => $this->shipment->aidRequest->location,
            ]),
            'url' => route('shipments.show', $this->shipment),
        ];
    }
}
