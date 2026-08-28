<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_viewer_can_download_the_receipt_for_a_delivered_shipment(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->delivered()->create();

        $response = $this->actingAs($admin)->get("/shipments/{$shipment->id}/receipt");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_receipt_is_not_available_before_delivery_is_confirmed(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create();

        $response = $this->actingAs($admin)->get("/shipments/{$shipment->id}/receipt");

        $response->assertNotFound();
    }

    public function test_unrelated_coordinator_cannot_download_someone_elses_receipt(): void
    {
        $owner = User::factory()->coordinator()->create();
        $stranger = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($owner)->create();
        $shipment = Shipment::factory()->delivered()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($stranger)->get("/shipments/{$shipment->id}/receipt");

        $response->assertForbidden();
    }

    public function test_owning_coordinator_can_download_the_receipt(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $aidRequest = AidRequest::factory()->for($coordinator)->create();
        $shipment = Shipment::factory()->delivered()->for($aidRequest, 'aidRequest')->create();

        $response = $this->actingAs($coordinator)->get("/shipments/{$shipment->id}/receipt");

        $response->assertOk();
    }
}
