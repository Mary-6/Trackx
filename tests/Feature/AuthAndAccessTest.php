<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Administrator Login');
    }

    public function test_no_public_registration(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_admin_can_log_in_and_out(): void
    {
        $user = User::factory()->superAdmin()->create(['password' => bcrypt('secret-pass-123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors();
        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass-123'])
            ->assertRedirect(route('dashboard'));

        $this->get('/admin')->assertOk();
        $this->post('/logout')->assertRedirect('/');
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_support_cannot_reach_super_admin_pages(): void
    {
        $support = User::factory()->support()->create();

        foreach (['/admin/users', '/admin/settings', '/admin/audit-log', '/admin/api-tokens'] as $path) {
            $this->actingAs($support)->get($path)->assertForbidden();
        }
    }

    public function test_manager_cannot_manage_users(): void
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
    }

    public function test_super_admin_can_manage_users(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Manager',
            'email' => 'mgr@example.com',
            'role' => 'manager',
            'password' => 'Str0ng-Passw0rd!',
            'password_confirmation' => 'Str0ng-Passw0rd!',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'mgr@example.com', 'role' => 'manager']);
    }

    public function test_last_super_admin_cannot_be_demoted(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'support',
            'is_active' => '1',
        ])->assertSessionHasErrors('role');
    }

    public function test_inactive_user_is_blocked_from_protected_areas(): void
    {
        $user = User::factory()->manager()->create(['is_active' => false]);
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_contact_form_stores_enquiry(): void
    {
        $this->post('/contact', [
            'name' => 'Jane Public',
            'email' => 'jane@example.com',
            'message' => 'Where is my parcel?',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', ['email' => 'jane@example.com', 'is_resolved' => false]);
    }

    public function test_contact_form_validates(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'not-an-email', 'message' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_settings_are_editable_by_super_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->put('/admin/settings', [
            'company_name' => 'New Brand Co',
            'notifications_enabled' => '1',
        ])->assertRedirect();

        $this->assertSame('New Brand Co', SiteSetting::getValue('company_name'));
    }
}
