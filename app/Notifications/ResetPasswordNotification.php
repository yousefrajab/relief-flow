<?php

namespace App\Notifications;

use App\Mail\ReliefFlowAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ReliefFlowAlertMail
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new ReliefFlowAlertMail(
            __('Reset your ReliefFlow password'),
            __('You are receiving this email because we received a password reset request for your account. This link will expire in 60 minutes. If you did not request a password reset, no further action is required.'),
            $url,
            __('Reset Password')
        ))->to($notifiable->getEmailForPasswordReset());
    }
}
