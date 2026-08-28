<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushService
{
    private string $publicKey;

    private string $privateKey;

    private string $subject;

    public function __construct()
    {
        $this->publicKey = config('services.webpush.public_key') ?? '';
        $this->privateKey = config('services.webpush.private_key') ?? '';
        $this->subject = config('services.webpush.subject') ?? '';
    }

    private function enabled(): bool
    {
        return $this->publicKey !== '' && $this->privateKey !== '';
    }

    public function sendToUser(User $user, string $title, string $body, string $url): void
    {
        $subscriptions = $user->pushSubscriptions;

        if ($subscriptions->isEmpty()) {
            return;
        }

        if (! $this->enabled()) {
            Log::info("[Simulation WebPush] To user #{$user->id} | Title: {$title} | Body: {$body}");

            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => $this->subject,
                    'publicKey' => $this->publicKey,
                    'privateKey' => $this->privateKey,
                ],
            ]);

            $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);

            foreach ($subscriptions as $subscription) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'publicKey' => $subscription->public_key,
                        'authToken' => $subscription->auth_token,
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
                    PushSubscription::where('user_id', $user->id)
                        ->where('endpoint', $report->getEndpoint())
                        ->delete();
                }
            }
        } catch (Throwable $e) {
            Log::warning('WebPushService::sendToUser failed: '.$e->getMessage());
        }
    }
}
