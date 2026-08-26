<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AidRequestLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_submit_a_multi_item_request(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $itemA = Item::factory()->create();
        $itemB = Item::factory()->create();

        $response = $this->actingAs($coordinator)->post('/aid-requests', [
            'location' => 'Deir El-Balah Distribution Point',
            'notes' => 'Urgent',
            'items' => [
                ['item_id' => $itemA->id, 'quantity' => 50],
                ['item_id' => $itemB->id, 'quantity' => 20],
            ],
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('aid_requests', [
            'user_id' => $coordinator->id,
            'location' => 'Deir El-Balah Distribution Point',
            'status' => 'pending',
        ]);
        $aidRequest = AidRequest::first();
        $this->assertCount(2, $aidRequest->requestItems);
    }

    public function test_depot_manager_cannot_submit_a_request(): void
    {
        $manager = User::factory()->depotManager()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($manager)->post('/aid-requests', [
            'location' => 'Somewhere',
            'items' => [['item_id' => $item->id, 'quantity' => 5]],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('aid_requests', ['location' => 'Somewhere']);
    }

    public function test_dispatch_deducts_stock_and_creates_shipment(): void
    {
        $manager = User::factory()->depotManager()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 120]);

        $response = $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_name' => 'Test Driver',
            'driver_phone' => '0599999999',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('dispatched', $aidRequest->fresh()->status);
        $this->assertSame(380, Inventory::where('warehouse_id', $warehouse->id)->where('item_id', $item->id)->first()->quantity);
        $this->assertDatabaseHas('shipments', [
            'aid_request_id' => $aidRequest->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'dispatched',
        ]);
    }

    public function test_dispatch_fails_when_stock_is_insufficient(): void
    {
        $manager = User::factory()->depotManager()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create(['warehouse_id' => $warehouse->id, 'item_id' => $item->id, 'quantity' => 10]);

        $aidRequest = AidRequest::factory()->create();
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 120]);

        $response = $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/dispatch", [
            'warehouse_id' => $warehouse->id,
            'driver_name' => 'Test Driver',
            'driver_phone' => '0599999999',
        ]);

        $response->assertSessionHasErrors('warehouse_id');
        $this->assertSame('pending', $aidRequest->fresh()->status);
        $this->assertSame(10, Inventory::where('warehouse_id', $warehouse->id)->where('item_id', $item->id)->first()->quantity);
        $this->assertDatabaseMissing('shipments', ['aid_request_id' => $aidRequest->id]);
    }

    public function test_depot_manager_can_reject_a_pending_request(): void
    {
        $manager = User::factory()->depotManager()->create();
        $aidRequest = AidRequest::factory()->create();

        $response = $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/reject", [
            'rejection_reason' => 'No matching stock available.',
        ]);

        $response->assertRedirect(route('dashboard'));
        $aidRequest->refresh();
        $this->assertSame('rejected', $aidRequest->status);
        $this->assertSame('No matching stock available.', $aidRequest->rejection_reason);
    }

    public function test_cannot_reject_an_already_dispatched_request(): void
    {
        $manager = User::factory()->depotManager()->create();
        $aidRequest = AidRequest::factory()->dispatched()->create();

        $response = $this->actingAs($manager)->post("/aid-requests/{$aidRequest->id}/reject", [
            'rejection_reason' => 'Too late.',
        ]);

        $response->assertForbidden();
        $this->assertSame('dispatched', $aidRequest->fresh()->status);
    }

    public function test_owning_coordinator_can_confirm_delivery(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($coordinator)->post("/shipments/{$shipment->id}/deliver");

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('delivered', $shipment->fresh()->status);
        $this->assertSame('delivered', $aidRequest->fresh()->status);
        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    public function test_unrelated_coordinator_cannot_confirm_someone_elses_delivery(): void
    {
        $owner = User::factory()->coordinator()->create();
        $stranger = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($owner)->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($stranger)->post("/shipments/{$shipment->id}/deliver");

        $response->assertForbidden();
        $this->assertSame('dispatched', $shipment->fresh()->status);
    }

    public function test_admin_can_confirm_delivery_on_behalf_of_any_request(): void
    {
        $admin = User::factory()->admin()->create();
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->dispatched()->create();
        $shipment = \App\Models\Shipment::factory()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($admin)->post("/shipments/{$shipment->id}/deliver");

        $response->assertRedirect(route('dashboard'));
        $this->assertSame('delivered', $shipment->fresh()->status);
    }
}
