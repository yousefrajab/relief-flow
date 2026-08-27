<?php

namespace App\Notifications;

use App\Models\AidRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AidRequestSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public AidRequest $aidRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $priorityLabel = match ($this->aidRequest->priority) {
            'critical' => __('Critical'),
            'high' => __('High Priority'),
            default => __('Normal'),
        };

        return [
            'message' => __('New :priority aid request from :location awaiting review.', [
                'priority' => $priorityLabel,
                'location' => $this->aidRequest->location,
            ]),
            'url' => route('aid-requests.show', $this->aidRequest),
        ];
    }
}
