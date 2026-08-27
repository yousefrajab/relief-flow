<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\AidRequestItem;
use App\Models\Item;
use App\Models\Shipment;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_tracking_page_shows_shipment_status(): void
    {
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create(['name' => 'Family Food Parcel']);
        $aidRequest = AidRequest::factory()->dispatched()->create(['location' => 'Khan Younis']);
        AidRequestItem::create(['aid_request_id' => $aidRequest->id, 'item_id' => $item->id, 'quantity' => 50]);
        $shipment = Shipment::factory()->for($aidRequest, 'aidRequest')->for($warehouse)->create([
            'qr_code_token' => 'RF-TESTTOKEN',
            'driver_phone' => '0599999999',
        ]);

        $response = $this->get('/track/RF-TESTTOKEN');

        $response->assertOk();
        $response->assertSee('RF-TESTTOKEN');
        $response->assertSee('Khan Younis');
        $response->assertSee('Family Food Parcel');
        $response->assertDontSee('0599999999');
    }

    public function test_public_tracking_page_404s_for_unknown_token(): void
    {
        $response = $this->get('/track/RF-DOESNOTEXIST');

        $response->assertNotFound();
    }
}
