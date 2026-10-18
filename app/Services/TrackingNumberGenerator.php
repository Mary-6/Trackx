<?php

namespace App\Services;

use App\Models\Shipment;
use Illuminate\Support\Str;

class TrackingNumberGenerator
{
    /**
     * Charset excludes ambiguous characters (I, O, 0, 1) for readability.
     */
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const LENGTH = 12;

    /**
     * Generate a unique tracking number — 12 mixed uppercase letters and digits,
     * guaranteed to contain at least one letter and one digit. No prefix.
     */
    public function generate(): string
    {
        do {
            $number = '';
            for ($i = 0; $i < self::LENGTH; $i++) {
                $number .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }
        } while (! $this->isValidFormat($number) || Shipment::where('tracking_number', $number)->exists());

        return $number;
    }

    /**
     * Validate the public format (not existence): 10–24 chars, uppercase letters
     * and digits, at least one of each, no separators.
     */
    public function isValidFormat(string $number): bool
    {
        $n = Str::upper(trim($number));

        return (bool) preg_match('/^(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{10,24}$/', $n);
    }
}
