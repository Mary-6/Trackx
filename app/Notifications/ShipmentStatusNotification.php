<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shipment $shipment,
        public string $type,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $company = setting('company_name', config('app.name'));
        $shipment = $this->shipment->loadMissing('status', 'category');

        $subject = match ($this->type) {
            'created' => "Your shipment {$shipment->tracking_number} has been created",
            'dispatched' => "Your shipment {$shipment->tracking_number} has been dispatched",
            'delivered' => "Your shipment {$shipment->tracking_number} has been delivered",
            'exception' => "Update required on shipment {$shipment->tracking_number}",
            default => "Update on your shipment {$shipment->tracking_number}",
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$shipment->recipient_name},")
            ->line($this->bodyLine($shipment))
            ->line("Tracking number: **{$shipment->tracking_number}**")
            ->line("Current status: {$shipment->status?->name}");

        if ($shipment->estimated_delivery_date) {
            $message->line('Estimated delivery: '.$shipment->estimated_delivery_date->toFormattedDateString());
        }

        return $message
            ->action('Track your shipment', url('/track/'.$shipment->tracking_number))
            ->line("Thank you for choosing {$company}.")
            ->salutation("— {$company} Support");
    }

    protected function bodyLine(Shipment $shipment): string
    {
        return match ($this->type) {
            'created' => 'A shipment has been registered for you. You can follow its progress at any time using your tracking number.',
            'dispatched' => 'Good news — your shipment is on its way.',
            'delivered' => 'Your shipment has been confirmed as delivered. We hope everything arrived safely.',
            'exception' => 'There has been a delivery exception on your shipment. Our team is reviewing it; please track the shipment for the latest update or contact support.',
            'delay' => 'Your shipment is experiencing a delay. We apologise for the inconvenience.',
            default => "Your shipment status has been updated to: {$shipment->status?->name}.",
        };
    }
}
