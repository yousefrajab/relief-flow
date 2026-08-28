<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\AccountSuspendedNotification;
use App\Notifications\AidRequestRejectedNotification;
use App\Notifications\AidRequestSubmittedNotification;
use App\Notifications\ShipmentDeliveredNotification;
use App\Notifications\ShipmentDispatchedNotification;
use App\Notifications\ShipmentPickedUpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_an_account_notifies_the_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $pending = User::factory()->coordinator()->pendingVerification()->create();

        $this->actingAs($admin)->post("/users/{$pending->id}/approve");

        Notification::assertSentTo($pending, AccountApprovedNotification::class);
    }

    public function test_suspending_an_account_notifies_the_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $active = User::factory()->coordinator()->create();

        $this->actingAs($admin)->post("/users/{$active->id}/reject");

        Notification::assertSentTo($active, AccountSuspendedNotification::class);
    }

    public function test_submitting_a_request_notifies_active_staff_only(): void
    {
        Notification::fake();

        $coordinator = User::factory()->coordinator()->create();
        $item = Item::factory()->create();
        $activeAdmin = User::factory()->admin()->create();
        $activeManager = User::factory()->depotManager()->create();
        $suspendedManager = User::factory()->depotManager()->create(['status' => 'suspended']);

        $this->actingAs($coordinator)->post('/aid-requests', [
            'location' => 'Somewhere far enough',
            'items' => [['item_id' => $item->id, 'quantity' => 5]],
        ]);

        Notification::assertSentTo($activeAdmin, AidRequestSubmittedNotification::class);
        Notification::assertSentTo($activeManager, AidRequestSubmittedNotification::class);
        Notification::assertNotSentTo($suspendedManager, AidRequestSubmittedNotification::class);
        Notification::assertNotSentTo($coordinator, AidRequestSubmittedNotification::class);
    }

    public function test_rejecting_a_request_notifies_its_coordinator(): void
    {
        Notification::fake();

        $manager = User::factory()->depotManager()->create();
        $aidRequest = AidRequest::factory()->create();

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/reject", [
            'rejection_reason' => 'No stock available.',
        ]);

        Notification::assertSentTo($aidRequest->user, AidRequestRejectedNotification::class);
    }

    public function test_dispatching_notifies_the_coordinator_and_sends_a_driver_sms(): void
    {
        Notification::fake();
        Mail::fake();

        $manager = User::factory()->depotManager()->create();
        $driver = User::factory()->driver()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        \App\Models\Inventory::create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->withArgs(fn ($message) => str_contains($message, '[Simulation SMS]'))
            ->atLeast()->once();
        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->withArgs(fn ($message) => str_contains($message, '[Simulation WhatsApp]'))
            ->atLeast()->once();

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_user_id' => $driver->id,
        ]);

        Notification::assertSentTo($aidRequest->user, ShipmentDispatchedNotification::class);
        Notification::assertSentTo($driver, \App\Notifications\DriverAssignedNotification::class);
    }

    public function test_delivery_notifies_active_staff(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->pickedUp()->for($aidRequest, 'aidRequest')->create();

        $this->actingAs($coordinator)->post("/shipments/{$shipment->id}/deliver");

        Notification::assertSentTo($admin, ShipmentDeliveredNotification::class);
    }

    public function test_pickup_notifies_active_staff(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup");

        Notification::assertSentTo($admin, ShipmentPickedUpNotification::class);
    }

    public function test_notification_bell_shows_unread_count_and_marking_read_redirects(): void
    {
        $user = User::factory()->coordinator()->create();
        $user->notify(new AccountApprovedNotification);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();

        $notificationId = $user->notifications()->first()->id;

        $readResponse = $this->actingAs($user)->get("/notifications/{$notificationId}/read");
        $readResponse->assertRedirect(route('dashboard'));

        $this->assertNotNull($user->notifications()->first()->read_at);
    }

    public function test_notification_poll_endpoint_returns_unread_count_and_items(): void
    {
        $user = User::factory()->coordinator()->create();
        $user->notify(new AccountApprovedNotification);

        $response = $this->actingAs($user)->getJson('/notifications/poll');

        $response->assertOk();
        $response->assertJsonStructure(['count', 'items' => [['id', 'message', 'url', 'created_at']]]);
        $response->assertJsonPath('count', 1);
    }

    public function test_mark_all_read_via_ajax_returns_json_without_redirect(): void
    {
        $user = User::factory()->coordinator()->create();
        $user->notify(new AccountApprovedNotification);

        $response = $this->actingAs($user)->postJson('/notifications/read-all');

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
        $this->assertSame(0, $user->unreadNotifications()->count());
    }
}
