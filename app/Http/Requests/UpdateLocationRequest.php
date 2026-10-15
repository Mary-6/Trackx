<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Used both by admins (manual updates, clearly labelled) and by the
 * authenticated integrations API endpoint.
 */
class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by route middleware / sanctum token ability
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_name' => ['nullable', 'string', 'max:160'],
            'recorded_at' => ['nullable', 'date', 'before_or_equal:+10 minutes'],
        ];
    }
}
