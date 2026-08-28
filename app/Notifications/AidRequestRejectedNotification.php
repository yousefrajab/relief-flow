<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use App\Models\AidRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AidRequestRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public AidRequest $aidRequest) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'webpush'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('Your aid request has been rejected'),
            __('Your aid request for :location was rejected. Reason: :reason', [
                'location' => $this->aidRequest->location,
                'reason' => $this->aidRequest->rejection_reason,
            ]),
            route('aid-requests.show', $this->aidRequest),
            __('View request')
        ))->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Your aid request for :location was rejected.', ['location' => $this->aidRequest->location]),
            'url' => route('aid-requests.show', $this->aidRequest),
        ];
    }
}
