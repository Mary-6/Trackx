<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrackingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    public function rules(): array
    {
        return [
            'shipment_status_id' => ['required', 'exists:shipment_statuses,id'],
            'location_name' => ['nullable', 'string', 'max:160'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'occurred_at' => ['nullable', 'date', 'before_or_equal:now'],
            'public_message' => ['nullable', 'string', 'max:1000'],
            'private_note' => ['nullable', 'string', 'max:1000'],
            'notify_recipient' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['notify_recipient' => $this->boolean('notify_recipient')]);
    }
}
