<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_a_request_logs_an_activity(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $item = Item::factory()->create();

        $this->actingAs($coordinator)->post('/aid-requests', [
            'location' => 'Activity Log Test Location',
            'items' => [['item_id' => $item->id, 'quantity' => 5]],
        ]);

        $aidRequest = AidRequest::first();

        $this->assertDatabaseHas('aid_request_activities', [
            'aid_request_id' => $aidRequest->id,
            'user_id' => $coordinator->id,
            'action' => 'submitted',
        ]);
    }

    public function test_rejecting_a_request_logs_an_activity_with_the_reason(): void
    {
        $manager = User::factory()->depotManager()->create();
        $aidRequest = AidRequest::factory()->create();

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/reject", [
            'rejection_reason' => 'No matching stock available.',
        ]);

        $this->assertDatabaseHas('aid_request_activities', [
            'aid_request_id' => $aidRequest->id,
            'user_id' => $manager->id,
            'action' => 'rejected',
            'notes' => 'No matching stock available.',
        ]);
    }

    public function test_dispatching_logs_an_activity(): void
    {
        $manager = User::factory()->depotManager()->create();
        $driver = User::factory()->driver()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_user_id' => $driver->id,
        ]);

        $this->assertDatabaseHas('aid_request_activities', [
            'aid_request_id' => $aidRequest->id,
            'user_id' => $manager->id,
            'action' => 'dispatched',
        ]);
    }

    public function test_confirming_delivery_logs_an_activity(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->pickedUp()->for($aidRequest, 'aidRequest')->create();

        $this->actingAs($coordinator)->post("/shipments/{$shipment->id}/deliver");

        $this->assertDatabaseHas('aid_request_activities', [
            'aid_request_id' => $aidRequest->id,
            'user_id' => $coordinator->id,
            'action' => 'delivered',
        ]);
    }

    public function test_full_activity_log_is_visible_in_order_on_the_request_page(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->depotManager()->create();
        $coordinator = User::factory()->coordinator()->create();
        $driver = User::factory()->driver()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $this->actingAs($coordinator)->post('/aid-requests', [
            'location' => 'Full Timeline Test',
            'items' => [['item_id' => $item->id, 'quantity' => 10]],
        ]);
        $aidRequest = AidRequest::first();

        $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_user_id' => $driver->id,
        ]);

        $shipment = $aidRequest->fresh()->shipment;
        $this->actingAs($driver)->post("/shipments/{$shipment->id}/pickup");
        $this->actingAs($coordinator)->post("/shipments/{$shipment->id}/deliver");

        $response = $this->actingAs($admin)->withSession(['locale' => 'en'])->get("/aid-requests/{$aidRequest->id}");

        $response->assertOk();
        $response->assertSeeInOrder([
            'submitted this request',
            'dispatched a shipment',
            'confirmed pickup from warehouse',
            'confirmed delivery',
        ]);
    }
}
