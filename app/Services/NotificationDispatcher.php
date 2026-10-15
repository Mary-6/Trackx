<?php

namespace App\Services;

use App\Models\NotificationRecord;
use App\Models\Shipment;
use App\Notifications\ShipmentStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationDispatcher
{
    /**
     * Notify a recipient about a shipment event. Always records the
     * attempt — success or failure — so admins can audit delivery.
     * De-duplicates pending/sent records for the same shipment+type+recipient.
     */
    public function notifyRecipient(Shipment $shipment, string $type): ?NotificationRecord
    {
        $recipient = $shipment->recipient_email;

        if (! $recipient) {
            return null;
        }

        if (setting('notifications_enabled', '1') !== '1') {
            return NotificationRecord::create([
                'shipment_id' => $shipment->id,
                'type' => $type,
                'channel' => 'mail',
                'recipient' => $recipient,
                'status' => NotificationRecord::STATUS_SKIPPED,
                'error' => 'Notifications disabled in site settings.',
            ]);
        }

        $existing = NotificationRecord::where('shipment_id', $shipment->id)
            ->where('type', $type)
            ->where('recipient', $recipient)
            ->whereIn('status', [NotificationRecord::STATUS_PENDING, NotificationRecord::STATUS_SENT])
            ->first();

        if ($existing) {
            return $existing;
        }

        $record = NotificationRecord::create([
            'shipment_id' => $shipment->id,
            'type' => $type,
            'channel' => 'mail',
            'recipient' => $recipient,
            'status' => NotificationRecord::STATUS_PENDING,
        ]);

        try {
            Notification::route('mail', $recipient)
                ->notifyNow(new ShipmentStatusNotification($shipment, $type));

            $record->update([
                'status' => NotificationRecord::STATUS_SENT,
                'sent_at' => now(),
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Shipment notification failed', [
                'shipment_id' => $shipment->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            $record->update([
                'status' => NotificationRecord::STATUS_FAILED,
                'error' => substr($e->getMessage(), 0, 1000),
            ]);
        }

        return $record;
    }
}
