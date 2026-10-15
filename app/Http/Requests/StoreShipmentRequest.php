<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageShipments() ?? false;
    }

    /**
     * Mandatory: category (or a new one), status, and a title or description.
     * Everything else is genuinely optional — partial records are valid and
     * can be completed later.
     */
    public function rules(): array
    {
        return [
            'tracking_number' => ['nullable', 'string', 'max:32', 'regex:/^[A-Z0-9][A-Z0-9\-]{4,31}$/i', 'unique:shipments,tracking_number'],
            'shipment_category_id' => ['required_without:category_name', 'nullable', 'exists:shipment_categories,id'],
            'category_name' => ['required_without:shipment_category_id', 'nullable', 'string', 'max:80'],
            'new_category' => ['nullable', 'string', 'max:80'],
            'shipment_status_id' => ['required', 'exists:shipment_statuses,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'title' => ['nullable', 'string', 'max:160'],
            'product_name' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'sender_name' => ['nullable', 'string', 'max:120'],
            'sender_email' => ['nullable', 'email', 'max:160'],
            'sender_phone' => ['nullable', 'string', 'max:40'],
            'sender_address' => ['nullable', 'string', 'max:255'],
            'sender_house_number' => ['nullable', 'string', 'max:30'],
            'sender_city' => ['nullable', 'string', 'max:80'],
            'sender_state' => ['nullable', 'string', 'max:80'],
            'sender_country' => ['nullable', 'string', 'max:80'],

            'recipient_name' => ['nullable', 'string', 'max:120'],
            'recipient_email' => ['nullable', 'email', 'max:160'],
            'recipient_phone' => ['nullable', 'string', 'max:40'],
            'recipient_address' => ['nullable', 'string', 'max:255'],
            'recipient_house_number' => ['nullable', 'string', 'max:30'],
            'recipient_city' => ['nullable', 'string', 'max:80'],
            'recipient_state' => ['nullable', 'string', 'max:80'],
            'recipient_country' => ['nullable', 'string', 'max:80'],

            'origin_country' => ['nullable', 'string', 'max:80'],
            'origin_city' => ['nullable', 'string', 'max:80'],
            'origin_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'origin_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'destination_country' => ['nullable', 'string', 'max:80'],
            'destination_city' => ['nullable', 'string', 'max:80'],
            'destination_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'destination_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'shipping_method' => ['nullable', 'string', 'max:60'],
            'dispatch_date' => ['nullable', 'date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'estimated_delivery_date' => ['nullable', 'date', 'after_or_equal:dispatch_date'],
            'delivery_time' => ['nullable', 'date_format:H:i'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'current_location_name' => ['nullable', 'string', 'max:160'],
            'dimensions' => ['nullable', 'string', 'max:60'],
            'customs_reference' => ['nullable', 'string', 'max:80'],
            'status_reason' => ['nullable', 'string', 'max:255'],
            'public_notes' => ['nullable', 'string', 'max:2000'],
            'private_notes' => ['nullable', 'string', 'max:2000'],
            'notify_recipient' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notify_recipient' => $this->boolean('notify_recipient'),
            'tracking_number' => $this->filled('tracking_number')
                ? strtoupper(trim((string) $this->input('tracking_number')))
                : null,
        ]);
    }
}
