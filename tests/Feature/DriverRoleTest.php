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

class DriverRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_as_a_driver_and_starts_pending(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'phone' => '0599999999',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'driver',
        ]);

        $response->assertRedirect(route('account.pending'));
        $this->assertDatabaseHas('users', [
            'email' => 'driver@example.com',
            'role' => 'driver',
            'status' => 'pending_verification',
        ]);
    }

    public function test_dispatch_requires_an_active_driver(): void
    {
        $manager = User::factory()->depotManager()->create();
        $suspendedDriver = User::factory()->driver()->create(['status' => 'suspended']);
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        $response = $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_user_id' => $suspendedDriver->id,
        ]);

        $response->assertSessionHasErrors('driver_user_id');
        $this->assertSame('pending', $aidRequest->fresh()->status);
    }

    public function test_dispatch_denormalizes_the_drivers_name_and_phone_onto_the_shipment(): void
    {
        $manager = User::factory()->depotManager()->create();
        $driver = User::factory()->driver()->create(['name' => 'Ahmad Driver', 'phone' => '0501234567']);
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_user_id' => $driver->id,
        ]);

        $this->assertDatabaseHas('shipments', [
            'aid_request_id' => $aidRequest->id,
            'driver_user_id' => $driver->id,
            'driver_name' => 'Ahmad Driver',
            'driver_phone' => '0501234567',
        ]);
    }

    public function test_driver_dashboard_only_shows_their_own_assigned_shipments(): void
    {
        $driver = User::factory()->driver()->create();
        $otherDriver = User::factory()->driver()->create();

        $myShipment = Shipment::factory()->create(['driver_user_id' => $driver->id]);
        Shipment::factory()->create(['driver_user_id' => $otherDriver->id]);

        $response = $this->actingAs($driver)->get('/dashboard');

        $response->assertOk();
        $response->assertSee($myShipment->qr_code_token);
        $response->assertViewHas('activeDeliveries', fn ($deliveries) => $deliveries->count() === 1);
    }

    public function test_assigned_driver_can_view_and_confirm_their_own_delivery(): void
    {
        $driver = User::factory()->driver()->create();
        $shipment = Shipment::factory()->create(['driver_user_id' => $driver->id]);

        $viewResponse = $this->actingAs($driver)->get("/shipments/{$shipment->id}");
        $viewResponse->assertOk();

        $deliverResponse = $this->actingAs($driver)->post("/shipments/{$shipment->id}/deliver");
        $deliverResponse->assertRedirect(route('shipments.show', $shipment));
        $this->assertSame('delivered', $shipment->fresh()->status);
    }

    public function test_driver_cannot_view_or_confirm_a_delivery_assigned_to_someone_else(): void
    {
        $driver = User::factory()->driver()->create();
        $otherDriver = User::factory()->driver()->create();
        $shipment = Shipment::factory()->create(['driver_user_id' => $otherDriver->id]);

        $viewResponse = $this->actingAs($driver)->get("/shipments/{$shipment->id}");
        $viewResponse->assertForbidden();

        $deliverResponse = $this->actingAs($driver)->post("/shipments/{$shipment->id}/deliver");
        $deliverResponse->assertForbidden();
        $this->assertSame('dispatched', $shipment->fresh()->status);
    }
}
