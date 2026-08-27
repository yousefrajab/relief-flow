<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        return (new ReliefFlowAlertMail(
            __('Your ReliefFlow account has been suspended'),
            __('An administrator has suspended your account. Please contact your organization for assistance.')
        ))->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('Your account has been suspended.'),
            'url' => route('account.pending'),
        ];
    }
}
