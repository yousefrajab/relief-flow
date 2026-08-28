<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'webpush'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('Your ReliefFlow account has been approved'),
            __('Welcome, :name. An administrator has approved your account and you can now sign in.', ['name' => $notifiable->name]),
            route('login'),
            __('Sign in')
        ))->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Your account has been approved.'),
            'url' => route('dashboard'),
        ];
    }
}
