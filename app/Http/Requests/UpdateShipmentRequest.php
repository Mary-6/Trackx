<?php

namespace App\Http\Requests;

class UpdateShipmentRequest extends StoreShipmentRequest
{
    /**
     * Same validation rules as creation, except:
     * - status is advanced only through tracking events, not the edit form
     * - tracking numbers are immutable once assigned
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['shipment_status_id'] = ['sometimes', 'nullable', 'exists:shipment_statuses,id'];
        $rules['tracking_number'] = ['prohibited'];

        return $rules;
    }
}
