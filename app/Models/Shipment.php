<?php

namespace App\Models;

use App\Services\Geocoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number', 'shipment_category_id', 'shipment_status_id', 'customer_id',
        'title', 'product_name', 'quantity', 'status_reason', 'description', 'image',
        'sender_name', 'sender_email', 'sender_phone', 'sender_address',
        'sender_house_number', 'sender_city', 'sender_state', 'sender_country',
        'recipient_name', 'recipient_email', 'recipient_phone', 'recipient_address',
        'recipient_house_number', 'recipient_city', 'recipient_state', 'recipient_country',
        'origin_country', 'origin_city', 'origin_latitude', 'origin_longitude',
        'destination_country', 'destination_city', 'destination_latitude', 'destination_longitude',
        'current_location_name', 'current_latitude', 'current_longitude', 'current_location_updated_at',
        'shipping_method', 'dispatch_date', 'departure_time', 'estimated_delivery_date', 'delivery_time', 'delivered_at',
        'weight_kg', 'dimensions', 'customs_reference',
        'public_notes', 'private_notes',
        'is_archived', 'archive_reason', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dispatch_date' => 'date',
            'estimated_delivery_date' => 'date',
            'delivered_at' => 'datetime',
            'current_location_updated_at' => 'datetime',
            'is_archived' => 'boolean',
            'quantity' => 'integer',
            'origin_latitude' => 'decimal:7',
            'origin_longitude' => 'decimal:7',
            'destination_latitude' => 'decimal:7',
            'destination_longitude' => 'decimal:7',
            'current_latitude' => 'decimal:7',
            'current_longitude' => 'decimal:7',
            'weight_kg' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShipmentCategory::class, 'shipment_category_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ShipmentStatus::class, 'shipment_status_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('occurred_at');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ShipmentLocation::class)->orderBy('recorded_at');
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(ShipmentLocation::class)->latestOfMany('recorded_at');
    }

    public function notificationRecords(): HasMany
    {
        return $this->hasMany(NotificationRecord::class);
    }

    /**
     * Public-safe representation for tracking pages / JSON endpoints.
     * Sender/receiver fields are gated by the admin-configured
     * `public_sender_fields` / `public_receiver_fields` settings — empty
     * values are always omitted rather than rendered as placeholders.
     */
    public function publicPayload(): array
    {
        $historyOrder = setting('tracking_history_order', 'desc') === 'asc'
            ? $this->trackingEvents->sortBy('occurred_at')
            : $this->trackingEvents->sortByDesc('occurred_at');

        return [
            'tracking_number' => $this->tracking_number,
            'title' => $this->title,
            'category' => $this->category?->name,
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'status' => [
                'name' => $this->status?->name,
                'slug' => $this->status?->slug,
                'color' => $this->status?->color,
            ],
            'status_reason' => $this->status_reason ?: $this->trackingEvents->last()?->public_message,
            'shipping_method' => $this->shipping_method,
            'origin' => ['country' => $this->origin_country, 'city' => $this->origin_city],
            'destination' => ['country' => $this->destination_country, 'city' => $this->destination_city],
            'current_location' => $this->current_location_name,
            'dispatch_date' => $this->dispatch_date?->toDateString(),
            'departure_time' => $this->departure_time ? substr((string) $this->departure_time, 0, 5) : null,
            'estimated_delivery_date' => $this->estimated_delivery_date?->toDateString(),
            'delivery_time' => $this->delivery_time ? substr((string) $this->delivery_time, 0, 5) : null,
            'delivered_at' => $this->delivered_at?->toDateTimeString(),
            'weight_kg' => $this->weight_kg !== null ? (float) $this->weight_kg : null,
            'sender' => $this->partyPayload('sender'),
            'receiver' => $this->partyPayload('recipient'),
            'public_notes' => $this->public_notes,
            'map' => $this->mapPayload(),
            'events' => $historyOrder
                ->values()
                ->map(fn (TrackingEvent $e) => [
                    'status' => $e->status?->name,
                    'location' => $e->location_name,
                    'occurred_at' => $e->occurred_at?->toDateTimeString(),
                    'date' => $e->occurred_at?->toDateString(),
                    'time' => $e->occurred_at?->format('H:i'),
                    'message' => $e->public_message,
                ])->all(),
        ];
    }

    /**
     * Only fields the administrator explicitly allows are exposed.
     * Defaults show a general name + locality only; contact details and
     * street-level addresses stay private unless enabled.
     */
    private function partyPayload(string $prefix): array
    {
        $settingKey = $prefix === 'recipient' ? 'public_receiver_fields' : 'public_sender_fields';
        $allowed = (array) json_decode(
            (string) setting($settingKey, '["name","city_state","country"]'),
            true
        );
        if ($allowed === []) {
            return [];
        }
        if ($allowed === []) {
            $allowed = ['name', 'city_state', 'country'];
        }

        $data = [
            'name' => $this->{"{$prefix}_name"},
            'email' => $this->{"{$prefix}_email"},
            'phone' => $this->{"{$prefix}_phone"},
            'house_number' => $this->{"{$prefix}_house_number"},
            'street' => $this->{"{$prefix}_address"},
            'city_state' => trim(collect([$this->{"{$prefix}_city"}, $this->{"{$prefix}_state"}])->filter()->implode(', ')) ?: null,
            'country' => $this->{"{$prefix}_country"},
        ];

        // Receivers fall back to the shipment destination; senders to origin.
        if ($prefix === 'recipient') {
            $data['city_state'] ??= trim(collect([$this->destination_city, null])->filter()->implode(', ')) ?: null;
            $data['country'] ??= $this->destination_country;
        } else {
            $data['city_state'] ??= $this->origin_city;
            $data['country'] ??= $this->origin_country;
        }

        $out = [];
        foreach ($allowed as $field) {
            $value = $data[$field] ?? null;
            if ($value !== null && $value !== '') {
                $out[$field] = $value;
            }
        }

        return $out;
    }

    public function mapPayload(): ?array
    {
        $geo = app(Geocoder::class);

        // Prefer recorded coordinates; otherwise geocode the location names
        // entered in shipment details so the map always has real markers.
        $originLabel = collect([$this->origin_city, $this->origin_country])->filter()->implode(', ') ?: null;
        $destLabel = collect([$this->destination_city, $this->destination_country])->filter()->implode(', ') ?: null;

        $origin = ($this->origin_latitude !== null)
            ? ['lat' => (float) $this->origin_latitude, 'lng' => (float) $this->origin_longitude]
            : $geo->resolve($originLabel);
        $origin && $origin += ['label' => $originLabel];

        $destination = ($this->destination_latitude !== null)
            ? ['lat' => (float) $this->destination_latitude, 'lng' => (float) $this->destination_longitude]
            : $geo->resolve($destLabel);
        $destination && $destination += ['label' => $destLabel];

        $current = null;
        if ($this->current_latitude !== null) {
            $current = ['lat' => (float) $this->current_latitude, 'lng' => (float) $this->current_longitude, 'label' => $this->current_location_name];
        } elseif ($pt = $geo->resolve($this->current_location_name)) {
            $current = $pt + ['label' => $this->current_location_name];
        }

        if (! $origin && ! $destination && ! $current) {
            return null;
        }

        return [
            'origin' => $origin,
            'destination' => $destination,
            'current' => $current,
            'path' => $this->locations
                ->whereNotNull('latitude')
                ->map(fn (ShipmentLocation $l) => [(float) $l->latitude, (float) $l->longitude])
                ->values()
                ->all(),
            'updated_at' => $this->current_location_updated_at?->toIso8601String(),
        ];
    }

    private function point($lat, $lng, ?string $label): ?array
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        return ['lat' => (float) $lat, 'lng' => (float) $lng, 'label' => $label];
    }

    public function isDelayed(): bool
    {
        return $this->estimated_delivery_date !== null
            && $this->estimated_delivery_date->isPast()
            && ! $this->delivered_at
            && ! $this->status?->is_final;
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(function ($w) use ($term) {
                $w->where('tracking_number', 'like', "%{$term}%")
                    ->orWhere('recipient_name', 'like', "%{$term}%")
                    ->orWhere('sender_name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            }))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('shipment_status_id', $v))
            ->when($filters['category'] ?? null, fn ($q, $v) => $q->where('shipment_category_id', $v))
            ->when($filters['destination'] ?? null, fn ($q, $v) => $q->where('destination_country', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['archived'] ?? null, fn ($q) => $q->where('is_archived', true))
            ->unless($filters['archived'] ?? null, fn ($q) => $q->where('is_archived', false));
    }
}
