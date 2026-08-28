<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_push_notifications(): void
    {
        $user = User::factory()->coordinator()->create();

        $response = $this->actingAs($user)->postJson('/push/subscribe', [
            'endpoint' => 'https://push.example.com/abc123',
            'keys' => [
                'p256dh' => 'test-public-key',
                'auth' => 'test-auth-token',
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);
    }

    public function test_subscribing_twice_with_the_same_endpoint_does_not_duplicate(): void
    {
        $user = User::factory()->coordinator()->create();

        $payload = [
            'endpoint' => 'https://push.example.com/abc123',
            'keys' => ['p256dh' => 'key-one', 'auth' => 'auth-one'],
        ];

        $this->actingAs($user)->postJson('/push/subscribe', $payload);

        $payload['keys'] = ['p256dh' => 'key-two', 'auth' => 'auth-two'];
        $this->actingAs($user)->postJson('/push/subscribe', $payload);

        $this->assertSame(1, $user->pushSubscriptions()->count());
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'key-two',
        ]);
    }

    public function test_user_can_unsubscribe(): void
    {
        $user = User::factory()->coordinator()->create();
        $user->pushSubscriptions()->create([
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);

        $response = $this->actingAs($user)->deleteJson('/push/subscribe', [
            'endpoint' => 'https://push.example.com/abc123',
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.com/abc123',
        ]);
    }

    public function test_a_user_cannot_delete_another_users_subscription(): void
    {
        $owner = User::factory()->coordinator()->create();
        $stranger = User::factory()->coordinator()->create();

        $owner->pushSubscriptions()->create([
            'endpoint' => 'https://push.example.com/abc123',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);

        $this->actingAs($stranger)->deleteJson('/push/subscribe', [
            'endpoint' => 'https://push.example.com/abc123',
        ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $owner->id,
            'endpoint' => 'https://push.example.com/abc123',
        ]);
    }

    public function test_guest_cannot_subscribe(): void
    {
        $response = $this->postJson('/push/subscribe', [
            'endpoint' => 'https://push.example.com/abc123',
            'keys' => ['p256dh' => 'key', 'auth' => 'token'],
        ]);

        $response->assertUnauthorized();
    }
}
