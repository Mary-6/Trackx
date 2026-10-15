<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->seed();

        foreach (['/', '/about', '/services', '/contact', '/faq', '/privacy-policy', '/terms', '/shipping-policy', '/track'] as $path) {
            $response = $this->get($path);
            $response->assertOk();
        }
    }

    public function test_homepage_shows_tracking_form_and_brand(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('Your Shipment. Tracked Every Step of the Way.')
            ->assertSee('tracking_number')
            ->assertSee(setting('company_name'));
    }

    public function test_sitemap_and_robots(): void
    {
        $this->seed();
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin', false);
    }

    public function test_smartsupp_widget_absent_without_key(): void
    {
        $this->seed();
        config(['services.smartsupp.key' => null]);

        $this->get('/')->assertOk()->assertDontSee('smartsuppchat.com', false);
    }

    public function test_smartsupp_widget_loads_once_when_key_set(): void
    {
        $this->seed();
        config(['services.smartsupp.key' => 'test-key-123']);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'smartsuppchat.com/loader.js'));
        $this->assertStringContainsString('test-key-123', $html);
    }
}
