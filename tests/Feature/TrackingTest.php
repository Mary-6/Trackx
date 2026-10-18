<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\SiteSetting;
use App\Models\TrackingEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_valid_tracking_lookup_shows_shipment(): void
    {
        $shipment = Shipment::factory()->create(['tracking_number' => 'B4N7P2D8S5TZ']);

        $this->post('/track', ['tracking_number' => 'b4n7p2d8s5tz'])
            ->assertRedirect('/track/B4N7P2D8S5TZ');

        $this->get('/track/B4N7P2D8S5TZ')
            ->assertOk()
            ->assertSee('B4N7P2D8S5TZ')
            ->assertSee('Shipment Created')
            ->assertSee('Geneva')
            ->assertSee('London');
    }

    public function test_invalid_tracking_number_shows_not_found(): void
    {
        $this->get('/track/ZX9K4J7W3H2M')
            ->assertOk()
            ->assertSee('Shipment Not Found');
    }

    public function test_public_tracking_never_exposes_private_data(): void
    {
        $shipment = Shipment::factory()->create([
            'recipient_email' => 'secret-recipient@example.com',
            'recipient_phone' => 'SECRET-PHONE-99',
            'recipient_address' => '999 Secret Street',
            'sender_email' => 'secret-sender@example.com',
            'private_notes' => 'INTERNAL_DO_NOT_LEAK',
        ]);

        TrackingEvent::factory()->for($shipment)->create(['private_note' => 'INTERNAL_EVENT_NOTE']);

        $response = $this->get('/track/'.$shipment->tracking_number)->assertOk();
        $html = $response->getContent();

        foreach (['secret-recipient@example.com', 'SECRET-PHONE-99', '999 Secret Street', 'secret-sender@example.com', 'INTERNAL_DO_NOT_LEAK', 'INTERNAL_EVENT_NOTE'] as $secret) {
            $this->assertStringNotContainsString($secret, $html, "Leaked: {$secret}");
        }
    }

    public function test_location_json_returns_only_approved_fields(): void
    {
        $shipment = Shipment::factory()->create([
            'current_latitude' => 50.0,
            'current_longitude' => 8.0,
            'recipient_email' => 'nope@example.com',
        ]);

        $data = $this->getJson('/track/'.$shipment->tracking_number.'/location.json')
            ->assertOk()
            ->assertJsonStructure(['status', 'map' => ['origin', 'destination', 'current', 'path', 'updated_at']])
            ->json();

        $this->assertEquals(50.0, $data['map']['current']['lat']);
        $this->assertArrayNotHasKey('recipient_email', $data);
        $this->assertArrayNotHasKey('private_notes', $data);
    }

    public function test_archived_shipment_is_not_trackable(): void
    {
        $shipment = Shipment::factory()->create(['is_archived' => true, 'archive_reason' => 'test']);

        $this->get('/track/'.$shipment->tracking_number)
            ->assertOk()
            ->assertSee('Shipment Not Found');
    }

    public function test_tracking_result_page_is_noindex(): void
    {
        $shipment = Shipment::factory()->create();

        $this->get('/track/'.$shipment->tracking_number)
            ->assertOk()
            ->assertSee('noindex', false);
    }

    public function test_tracking_result_renders_dynamic_barcode(): void
    {
        $shipment = Shipment::factory()->create(['tracking_number' => 'M6R8T3P9K2XV']);

        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();

        // picqer renders an inline SVG barcode generated from the actual number
        $this->assertStringContainsString('<g id="bars"', $html);
        $this->assertStringContainsString('<desc>M6R8T3P9K2XV</desc>', $html);
        $this->assertStringContainsString('PACKAGE TRACKING INFORMATION', strtoupper($html));
    }

    public function test_empty_fields_are_omitted_from_public_page(): void
    {
        $shipment = Shipment::factory()->create([
            'title' => 'Partial consignment',
            'product_name' => null,
            'quantity' => null,
            'weight_kg' => null,
            'shipping_method' => null,
            'dispatch_date' => null,
            'estimated_delivery_date' => null,
            'recipient_name' => null,
            'recipient_email' => null,
            'recipient_phone' => null,
            'recipient_address' => null,
            'sender_name' => null,
            'sender_email' => null,
            'sender_phone' => null,
            'sender_address' => null,
            'origin_city' => null,
            'origin_country' => null,
            'origin_latitude' => null,
            'origin_longitude' => null,
            'destination_city' => null,
            'destination_country' => null,
            'destination_latitude' => null,
            'destination_longitude' => null,
            'current_location_name' => null,
            'current_latitude' => null,
            'current_longitude' => null,
            'public_notes' => null,
        ]);

        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();

        $this->assertStringContainsString('Partial consignment', $html);
        // No placeholder garbage
        foreach (['N/A', 'null', 'undefined', '##'] as $bad) {
            $this->assertStringNotContainsString('<dd class="font-medium text-gold-600 sm:flex-1">'.$bad, $html);
        }
        // Absent fields don't render their labels
        $this->assertStringNotContainsString('Product Name', $html);
        $this->assertStringNotContainsString('Weight', $html);
        // No receiver data at all → no receiver section
        $this->assertStringNotContainsString('Receiver Information', $html);
        $this->assertStringNotContainsString('Sender Information', $html);
    }

    public function test_receiver_privacy_fields_are_admin_configurable(): void
    {
        $shipment = Shipment::factory()->create([
            'recipient_name' => 'Visible Name',
            'recipient_email' => 'hidden-email@example.com',
            'recipient_phone' => '+1999000111',
        ]);

        // Default: name + locality only
        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();
        $this->assertStringContainsString('Visible Name', $html);
        $this->assertStringContainsString('Receiver Information', $html);
        $this->assertStringNotContainsString('hidden-email@example.com', $html);
        $this->assertStringNotContainsString('+1999000111', $html);

        // Admin enables email → it appears
        SiteSetting::setValue('public_receiver_fields', '["name","email"]', 'privacy');
        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();
        $this->assertStringContainsString('hidden-email@example.com', $html);

        // Admin disables everything → section omitted
        SiteSetting::setValue('public_receiver_fields', '[]', 'privacy');
        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();
        $this->assertStringNotContainsString('Receiver Information', $html);
    }

    public function test_shipment_history_order_is_configurable(): void
    {
        $shipment = Shipment::factory()->create();
        $statuses = ShipmentStatus::pluck('id', 'slug');

        TrackingEvent::factory()->for($shipment)->create([
            'shipment_status_id' => $statuses['shipment-created'],
            'occurred_at' => now()->subDays(3),
            'public_message' => 'FIRST_EVENT_MSG',
        ]);
        TrackingEvent::factory()->for($shipment)->create([
            'shipment_status_id' => $statuses['in-transit'],
            'occurred_at' => now(),
            'public_message' => 'LAST_EVENT_MSG',
        ]);

        // Scope to the history table — the latest event message is also used
        // as the "reason" line near the top of the page.
        $history = fn (string $html) => substr($html, strpos($html, 'Shipment History'));

        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();
        $section = $history($html);
        $this->assertLessThan(strpos($section, 'FIRST_EVENT_MSG'), strpos($section, 'LAST_EVENT_MSG'));

        SiteSetting::setValue('tracking_history_order', 'asc', 'privacy');
        $html = $this->get('/track/'.$shipment->tracking_number)->assertOk()->getContent();
        $section = $history($html);
        $this->assertLessThan(strpos($section, 'LAST_EVENT_MSG'), strpos($section, 'FIRST_EVENT_MSG'));
    }
}
