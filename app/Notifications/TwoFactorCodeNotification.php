<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('Your ReliefFlow verification code'),
            __('Your verification code is :code. It expires in 10 minutes. If you did not attempt to sign in, you can ignore this email.', ['code' => $this->code])
        ))->to($notifiable->email);
    }
}
