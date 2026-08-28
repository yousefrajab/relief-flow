<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_ask_about_pending_requests(): void
    {
        $admin = User::factory()->admin()->create();
        AidRequest::factory()->count(3)->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'كم طلب معلق عندنا؟',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', '3 aid request(s) are currently pending review.');
    }

    public function test_admin_can_ask_about_low_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $warehouse = Warehouse::factory()->create(['name' => 'Central Gaza Depot']);
        $item = Item::factory()->create(['name' => 'Blankets']);
        Inventory::factory()->create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 50]);

        $response = $this->actingAs($admin)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'is there any low stock right now?',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'Blankets') && str_contains($reply, 'Central Gaza Depot'));
    }

    public function test_coordinator_only_sees_their_own_request_counts(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $otherCoordinator = User::factory()->coordinator()->create();
        AidRequest::factory()->for($coordinator)->create(['status' => 'pending']);
        AidRequest::factory()->count(5)->for($otherCoordinator)->create(['status' => 'pending']);

        $response = $this->actingAs($coordinator)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'What is the status of my requests?',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', 'You have 1 pending, 0 dispatched, and 0 delivered request(s).');
    }

    public function test_driver_can_ask_about_their_own_deliveries(): void
    {
        $driver = User::factory()->driver()->create();
        $otherDriver = User::factory()->driver()->create();
        Shipment::factory()->create(['driver_user_id' => $driver->id, 'status' => 'dispatched']);
        Shipment::factory()->count(2)->create(['driver_user_id' => $otherDriver->id, 'status' => 'dispatched']);

        $response = $this->actingAs($driver)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'كم عندي توصيلة؟',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', 'You have 1 active delivery task(s) and have completed 0 so far.');
    }

    public function test_can_track_an_authorized_shipment_by_token(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create(['location' => 'Rafah Crossing']);
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['qr_code_token' => 'RF-ABC12345']);

        $response = $this->actingAs($coordinator)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'where is RF-ABC12345',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'RF-ABC12345') && str_contains($reply, 'Rafah Crossing'));
    }

    public function test_cannot_track_a_shipment_belonging_to_someone_else(): void
    {
        $stranger = User::factory()->coordinator()->create();
        $owner = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($owner)->dispatched()->create();
        Shipment::factory()->for($aidRequest, 'aidRequest')->create(['qr_code_token' => 'RF-SECRET1']);

        $response = $this->actingAs($stranger)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'RF-SECRET1',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', 'No shipment found with that tracking token, or you do not have access to it.');
    }

    public function test_shipment_tracking_bypasses_the_llm_even_when_a_real_api_key_is_configured(): void
    {
        config(['services.openai.key' => 'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake();

        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create(['location' => 'Rafah Crossing']);
        Shipment::factory()->for($aidRequest, 'aidRequest')->create(['qr_code_token' => 'RF-ABC12345']);

        $response = $this->actingAs($coordinator)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'where is RF-ABC12345',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'RF-ABC12345') && str_contains($reply, 'Rafah Crossing'));
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_unrecognized_question_gets_a_helpful_fallback(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->withSession(['locale' => 'en'])->postJson('/assistant/ask', [
            'message' => 'asdkjaslkdj random gibberish',
        ]);

        $response->assertOk();
        $response->assertJsonPath('reply', fn ($reply) => str_contains($reply, 'pending requests'));
    }

    public function test_message_is_required(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/assistant/ask', []);

        $response->assertStatus(422);
    }
}
