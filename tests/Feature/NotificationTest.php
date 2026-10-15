<?php

namespace Tests\Feature;

use App\Models\NotificationRecord;
use App\Models\Shipment;
use App\Models\SiteSetting;
use App\Notifications\ShipmentStatusNotification;
use App\Services\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_notification_is_recorded_when_sent(): void
    {
        Notification::fake();
        $shipment = Shipment::factory()->create(['recipient_email' => 'r@example.com']);

        $record = app(NotificationDispatcher::class)->notifyRecipient($shipment, 'created');

        $this->assertSame('sent', $record->status);
        Notification::assertSentOnDemand(ShipmentStatusNotification::class);
    }

    public function test_failure_is_recorded_not_hidden(): void
    {
        // Force the "log" mailer off a cliff by making the notification throw via a bad channel setup:
        // simplest deterministic path — point MAIL at an invalid SMTP host.
        config(['mail.default' => 'smtp']);
        config(['mail.mailers.smtp.host' => '127.0.0.1']);
        config(['mail.mailers.smtp.port' => 1]);
        config(['mail.mailers.smtp.timeout' => 2]);

        $shipment = Shipment::factory()->create(['recipient_email' => 'r@example.com']);
        $record = app(NotificationDispatcher::class)->notifyRecipient($shipment, 'created');

        $this->assertSame('failed', $record->status);
        $this->assertNotEmpty($record->error);
    }

    public function test_duplicate_notifications_are_not_resent(): void
    {
        Notification::fake();
        $shipment = Shipment::factory()->create(['recipient_email' => 'r@example.com']);
        $dispatcher = app(NotificationDispatcher::class);

        $first = $dispatcher->notifyRecipient($shipment, 'created');
        $second = $dispatcher->notifyRecipient($shipment, 'created');

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, NotificationRecord::count());
    }

    public function test_notifications_can_be_disabled_in_settings(): void
    {
        Notification::fake();
        SiteSetting::setValue('notifications_enabled', '0');

        $shipment = Shipment::factory()->create(['recipient_email' => 'r@example.com']);
        $record = app(NotificationDispatcher::class)->notifyRecipient($shipment, 'created');

        $this->assertSame('skipped', $record->status);
        Notification::assertNothingSent();
    }
}
