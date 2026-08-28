<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ShipmentPickupConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_driver_can_confirm_pickup(): void
    {
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $response = $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup");

        $response->assertRedirect(route('shipments.show', $shipment));
        $shipment->refresh();
        $this->assertSame('picked_up', $shipment->status);
        $this->assertNotNull($shipment->picked_up_at);

        $this->assertDatabaseHas('aid_request_activities', [
            'aid_request_id' => $aidRequest->id,
            'user_id' => $driver->id,
            'action' => 'picked_up',
        ]);
    }

    public function test_pickup_photo_is_stored_and_ai_verified(): void
    {
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup", [
            'pickup_photo' => UploadedFile::fake()->image('load.jpg'),
        ]);

        $shipment->refresh();
        $this->assertNotNull($shipment->pickup_photo_path);
        $this->assertSame('needs_review', $shipment->pickup_ai_verification_status);
    }

    public function test_unassigned_driver_cannot_confirm_pickup(): void
    {
        $driver = User::factory()->driver()->create();
        $otherDriver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $otherDriver->id]);

        $response = $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup");

        $response->assertForbidden();
        $this->assertSame('dispatched', $shipment->fresh()->status);
    }

    public function test_coordinator_cannot_confirm_pickup(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($coordinator)->post("/shipments/{$shipment->id}/pickup");

        $response->assertForbidden();
        $this->assertSame('dispatched', $shipment->fresh()->status);
    }

    public function test_admin_can_confirm_pickup_on_behalf_of_any_shipment(): void
    {
        $admin = User::factory()->admin()->create();
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $response = $this->actingAs($admin)->post("/shipments/{$shipment->id}/pickup");

        $response->assertRedirect(route('shipments.show', $shipment));
        $this->assertSame('picked_up', $shipment->fresh()->status);
    }

    public function test_delivery_cannot_be_confirmed_before_pickup_is_confirmed(): void
    {
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $response = $this->actingAs($driver)->post("/shipments/{$shipment->id}/deliver");

        $response->assertForbidden();
        $this->assertSame('dispatched', $shipment->fresh()->status);
        $this->assertNull($shipment->fresh()->delivered_at);
    }

    public function test_cannot_confirm_pickup_twice(): void
    {
        $driver = User::factory()->driver()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();
        $shipment = Shipment::factory()->pickedUp()->for($aidRequest, 'aidRequest')->create(['driver_user_id' => $driver->id]);

        $response = $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup");

        $response->assertForbidden();
    }
}
