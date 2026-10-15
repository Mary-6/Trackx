<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Str;

class TrackingNumberGenerator
{
    /**
     * Generate a unique, short public tracking number.
     * Format: ACE-XXXXXX — Aurevia Courier Express prefix + 6 random digits.
     */
    public function generate(): string
    {
        do {
            $number = 'ACE-'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Shipment::where('tracking_number', $number)->exists());

        return $number;
    }

    /**
     * Validate the public format (not existence).
     */
    public function isValidFormat(string $number): bool
    {
        return (bool) preg_match('/^ACE-\d{6}$/', Str::upper(trim($number)));
    }
}
