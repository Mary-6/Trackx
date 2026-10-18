<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\ShipmentStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShipmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function shipmentData(array $overrides = []): array
    {
        return array_merge([
            'shipment_category_id' => ShipmentCategory::first()->id,
            'shipment_status_id' => ShipmentStatus::where('slug', 'shipment-created')->value('id'),
            'description' => 'Test parcel',
            'sender_name' => 'Sender One',
            'recipient_name' => 'Recipient Two',
            'recipient_email' => 'recipient@example.com',
            'origin_city' => 'Geneva',
            'origin_country' => 'Switzerland',
            'destination_city' => 'London',
            'destination_country' => 'United Kingdom',
        ], $overrides);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->post('/admin/shipments')->assertRedirect(route('login'));
    }

    public function test_manager_can_create_shipment_with_generated_tracking_number(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)
            ->post('/admin/shipments', $this->shipmentData())
            ->assertRedirect();

        $shipment = Shipment::latest()->first();
        $this->assertNotNull($shipment);
        $this->assertMatchesRegularExpression('/^(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{10,24}$/', $shipment->tracking_number);
        $this->assertSame('shipment-created', $shipment->status->slug);
        $this->assertCount(1, $shipment->trackingEvents);
    }

    public function test_shipment_can_be_created_with_only_mandatory_fields(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)
            ->post('/admin/shipments', [
                'shipment_category_id' => ShipmentCategory::first()->id,
                'shipment_status_id' => ShipmentStatus::where('slug', 'shipment-created')->value('id'),
                'description' => 'Bare-bones consignment',
            ])
            ->assertRedirect();

        $shipment = Shipment::latest()->first();
        $this->assertSame('Bare-bones consignment', $shipment->description);
        $this->assertNotNull($shipment->tracking_number);
        $this->assertNull($shipment->recipient_name);
        $this->assertNull($shipment->origin_city);
    }

    public function test_manual_tracking_number_is_accepted_when_unique(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)
            ->post('/admin/shipments', $this->shipmentData(['tracking_number' => 'CUSTMN2026AB']))
            ->assertRedirect();

        $this->assertDatabaseHas('shipments', ['tracking_number' => 'CUSTMN2026AB']);

        $this->actingAs($user)
            ->post('/admin/shipments', $this->shipmentData(['tracking_number' => 'CUSTMN2026AB']))
            ->assertSessionHasErrors('tracking_number');
    }

    public function test_new_category_can_be_defined_inline(): void
    {
        $user = User::factory()->manager()->create();

        $this->actingAs($user)
            ->post('/admin/shipments', $this->shipmentData([
                'shipment_category_id' => null,
                'category_name' => 'Musical Instruments',
            ]));

        $this->assertDatabaseHas('shipment_categories', ['name' => 'Musical Instruments']);
    }

    public function test_support_agent_cannot_create_shipment(): void
    {
        $user = User::factory()->support()->create();

        $this->actingAs($user)
            ->post('/admin/shipments', $this->shipmentData())
            ->assertForbidden();
    }

    public function test_support_agent_can_view_but_not_edit(): void
    {
        $shipment = Shipment::factory()->create();
        $support = User::factory()->support()->create();

        $this->actingAs($support)->get("/admin/shipments/{$shipment->id}")->assertOk();
        $this->actingAs($support)->get("/admin/shipments/{$shipment->id}/edit")->assertForbidden();
    }

    public function test_add_event_appends_history_and_advances_status(): void
    {
        $user = User::factory()->manager()->create();
        $shipment = Shipment::factory()->create();
        $inTransit = ShipmentStatus::where('slug', 'in-transit')->first();

        $this->actingAs($user)
            ->post("/admin/shipments/{$shipment->id}/events", [
                'shipment_status_id' => $inTransit->id,
                'location_name' => 'Paris hub',
                'public_message' => 'In transit.',
            ])
            ->assertRedirect();

        $shipment->refresh();
        $this->assertSame($inTransit->id, $shipment->shipment_status_id);
        $this->assertSame('Paris hub', $shipment->current_location_name);

        // Original event preserved
        $this->assertCount(1, $shipment->trackingEvents);
    }

    public function test_delivered_event_sets_delivered_at(): void
    {
        $user = User::factory()->manager()->create();
        $shipment = Shipment::factory()->create();
        $delivered = ShipmentStatus::where('slug', 'delivered')->first();

        $this->actingAs($user)
            ->post("/admin/shipments/{$shipment->id}/events", ['shipment_status_id' => $delivered->id]);

        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    public function test_manual_location_update_is_recorded(): void
    {
        $user = User::factory()->manager()->create();
        $shipment = Shipment::factory()->create();

        $this->actingAs($user)
            ->post("/admin/shipments/{$shipment->id}/location", [
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'location_name' => 'Paris',
            ])
            ->assertRedirect();

        $location = $shipment->fresh()->latestLocation;
        $this->assertSame('manual', $location->source);
        $this->assertSame(48.8566, (float) $location->latitude);
    }

    public function test_archive_requires_reason_and_hides_from_tracking(): void
    {
        $user = User::factory()->manager()->create();
        $shipment = Shipment::factory()->create();

        $this->actingAs($user)
            ->post("/admin/shipments/{$shipment->id}/archive", [])
            ->assertSessionHasErrors('archive_reason');

        $this->actingAs($user)
            ->post("/admin/shipments/{$shipment->id}/archive", ['archive_reason' => 'Duplicate entry'])
            ->assertRedirect();

        $this->assertTrue($shipment->fresh()->is_archived);
        $this->get('/track/'.$shipment->tracking_number)->assertSee('Shipment Not Found');
    }

    public function test_shipment_index_filters_and_paginates(): void
    {
        $user = User::factory()->manager()->create();
        Shipment::factory()->count(3)->create();

        $this->actingAs($user)->get('/admin/shipments')->assertOk()->assertSee('Tracking');

        $statusId = Shipment::first()->shipment_status_id;
        $this->actingAs($user)->get('/admin/shipments?status='.$statusId)->assertOk();
        $this->actingAs($user)->get('/admin/shipments?status=99999')->assertOk();
    }

    public function test_csv_export_is_authorized_and_streams(): void
    {
        $manager = User::factory()->manager()->create();
        $support = User::factory()->support()->create();
        Shipment::factory()->count(2)->create();

        $this->actingAs($support)->get('/admin/shipments/export')->assertForbidden();

        $response = $this->actingAs($manager)->get('/admin/shipments/export');
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_search_finds_shipment_by_tracking_number(): void
    {
        $user = User::factory()->manager()->create();
        Shipment::factory()->create(['tracking_number' => 'W8K3L2M9Q1RX']);
        Shipment::factory()->count(2)->create();

        $this->actingAs($user)
            ->get('/admin/shipments?q=W8K3L2M')
            ->assertOk()
            ->assertSee('W8K3L2M9Q1RX');
    }
}
