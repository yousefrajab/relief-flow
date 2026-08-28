<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\User;
use App\Notifications\AidRequestSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WebPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifying_a_user_with_no_push_subscription_does_not_attempt_to_send(): void
    {
        Log::spy();

        $user = User::factory()->admin()->create();
        $aidRequest = AidRequest::factory()->create();

        $user->notify(new AidRequestSubmittedNotification($aidRequest));

        Log::shouldNotHaveReceived('info');
    }

    public function test_notifying_a_user_with_a_push_subscription_runs_in_simulation_mode_without_a_vapid_key(): void
    {
        Log::spy();

        $user = User::factory()->admin()->create();
        $user->pushSubscriptions()->create([
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);
        $aidRequest = AidRequest::factory()->create();

        $user->notify(new AidRequestSubmittedNotification($aidRequest));

        Log::shouldHaveReceived('info')->once()->withArgs(
            fn (string $message) => str_contains($message, '[Simulation WebPush]') && str_contains($message, "user #{$user->id}")
        );
    }

    public function test_a_malformed_subscription_does_not_crash_notification_sending_with_a_real_vapid_key(): void
    {
        Log::spy();

        config([
            'services.webpush.public_key' => 'BGPgwkGQ2EaFJFPH8KUuimnQYePNs1VYS9FpTB_pik5QBSTYiG-sqwWTsQsU6jme3fW-HGKkKQXJE5naHSCdarE',
            'services.webpush.private_key' => 'Z17SvqoxFRNidv4W09rFXj4-7fCIEHF3CoxgXuirD0w',
            'services.webpush.subject' => 'mailto:test@example.com',
        ]);

        $user = User::factory()->admin()->create();
        $user->pushSubscriptions()->create([
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'not-a-valid-uncompressed-ec-key',
            'auth_token' => 'test-auth-token',
        ]);
        $aidRequest = AidRequest::factory()->create();

        $user->notify(new AidRequestSubmittedNotification($aidRequest));

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $message) => str_contains($message, 'WebPushService::sendToUser failed')
        );
    }
}
