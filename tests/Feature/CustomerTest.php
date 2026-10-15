<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_list_requires_auth(): void
    {
        $this->get('/admin/customers')->assertRedirect(route('login'));
    }

    public function test_customers_are_searchable(): void
    {
        $user = User::factory()->manager()->create();
        Customer::factory()->create(['name' => 'Findable Person']);
        Customer::factory()->count(3)->create();

        $this->actingAs($user)
            ->get('/admin/customers?q=Findable')
            ->assertOk()
            ->assertSee('Findable Person');
    }

    public function test_customer_shows_linked_shipments(): void
    {
        $user = User::factory()->manager()->create();
        $customer = Customer::factory()->create();
        $shipment = Shipment::factory()->for($customer)->create();

        $this->actingAs($user)
            ->get("/admin/customers/{$customer->id}")
            ->assertOk()
            ->assertSee($shipment->tracking_number);
    }

    public function test_support_cannot_see_contact_details(): void
    {
        $support = User::factory()->support()->create();
        $customer = Customer::factory()->create(['phone' => 'HIDDEN-PHONE-123', 'address' => 'HIDDEN ADDRESS']);

        $response = $this->actingAs($support)->get("/admin/customers/{$customer->id}")->assertOk();

        $this->assertStringNotContainsString('HIDDEN-PHONE-123', $response->getContent());
        $this->assertStringNotContainsString('HIDDEN ADDRESS', $response->getContent());
    }

    public function test_manager_can_see_and_update_contact_details(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = Customer::factory()->create(['phone' => 'VISIBLE-PHONE']);

        $this->actingAs($manager)->get("/admin/customers/{$customer->id}")->assertSee('VISIBLE-PHONE');

        $this->actingAs($manager)
            ->put("/admin/customers/{$customer->id}", [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '555',
            ])->assertRedirect();

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Updated Name']);
    }

    public function test_support_cannot_update_customer(): void
    {
        $support = User::factory()->support()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($support)
            ->put("/admin/customers/{$customer->id}", ['name' => 'X'])
            ->assertForbidden();
    }

    public function test_duplicate_flagging(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($manager)->post("/admin/customers/{$customer->id}/flag")->assertRedirect();
        $this->assertTrue($customer->fresh()->is_flagged_duplicate);
    }
}
