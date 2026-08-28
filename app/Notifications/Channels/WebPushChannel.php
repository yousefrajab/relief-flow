<?php

namespace App\Notifications\Channels;

use App\Services\WebPushService;
use Illuminate\Notifications\Notification;

class WebPushChannel
{
    public function __construct(private WebPushService $webPush) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toArray')) {
            return;
        }

        $data = $notification->toArray($notifiable);

        $this->webPush->sendToUser(
            $notifiable,
            config('app.name'),
            $data['message'] ?? '',
            $data['url'] ?? route('dashboard')
        );
    }
}
