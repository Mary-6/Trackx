<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackingLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:32'],
        ];
    }

    public function trackingNumber(): string
    {
        // Customers may type/copy numbers with hyphens or spaces — strip them.
        return strtoupper(preg_replace('/[\s\-_]/', '', (string) $this->input('tracking_number')));
    }
}
