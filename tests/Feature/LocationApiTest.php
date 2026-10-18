<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $shipment = Shipment::factory()->create();

        $this->postJson("/api/v1/shipments/{$shipment->tracking_number}/location", [
            'latitude' => 10, 'longitude' => 10,
        ])->assertUnauthorized();
    }

    public function test_token_without_ability_is_forbidden(): void
    {
        $shipment = Shipment::factory()->create();
        $token = User::factory()->superAdmin()->create()->createToken('device', ['other-ability'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/shipments/{$shipment->tracking_number}/location", [
                'latitude' => 10, 'longitude' => 10,
            ])->assertForbidden();
    }

    public function test_authorized_token_records_location(): void
    {
        $shipment = Shipment::factory()->create();
        $token = User::factory()->superAdmin()->create()->createToken('gps', ['update-location'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/shipments/{$shipment->tracking_number}/location", [
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'location_name' => 'Madrid hub',
            ])
            ->assertAccepted()
            ->assertJsonPath('location.latitude', 40.4168);

        $shipment->refresh();
        $this->assertSame(40.4168, (float) $shipment->current_latitude);
        $this->assertSame('Madrid hub', $shipment->current_location_name);
        $this->assertSame('integration', $shipment->latestLocation->source);
    }

    public function test_invalid_coordinates_rejected(): void
    {
        $shipment = Shipment::factory()->create();
        $token = User::factory()->superAdmin()->create()->createToken('gps', ['update-location'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/shipments/{$shipment->tracking_number}/location", [
                'latitude' => 95, 'longitude' => 200,
            ])->assertUnprocessable();
    }

    public function test_future_timestamp_rejected(): void
    {
        $shipment = Shipment::factory()->create();
        $token = User::factory()->superAdmin()->create()->createToken('gps', ['update-location'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/shipments/{$shipment->tracking_number}/location", [
                'latitude' => 10,
                'longitude' => 10,
                'recorded_at' => now()->addHour()->toIso8601String(),
            ])->assertUnprocessable();
    }

    public function test_unknown_tracking_number_returns_404(): void
    {
        $token = User::factory()->superAdmin()->create()->createToken('gps', ['update-location'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/shipments/ZX9K4J7W3H2M/location', [
                'latitude' => 10, 'longitude' => 10,
            ])->assertNotFound();
    }
}
