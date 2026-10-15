<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_INTEGRATION = 'integration';

    public const SOURCE_SYSTEM = 'system';

    protected $fillable = [
        'shipment_id', 'shipment_status_id', 'location_name',
        'latitude', 'longitude', 'occurred_at',
        'public_message', 'private_note', 'source', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ShipmentStatus::class, 'shipment_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
