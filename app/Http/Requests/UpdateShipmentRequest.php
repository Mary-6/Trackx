<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateShipmentRequest extends StoreShipmentRequest
{
    /**
     * Same validation rules as creation, except:
     * - the tracking number may be changed, but must stay unique and well-formed
     * - status may be changed; the controller records a milestone when it differs
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['tracking_number'] = [
            'nullable', 'string', 'max:32',
            'regex:/^(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{10,24}$/',
            Rule::unique('shipments', 'tracking_number')->ignore($this->route('shipment')?->id),
        ];
        $rules['shipment_status_id'] = ['required', 'exists:shipment_statuses,id'];

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        // An empty tracking-number field means "keep the current one" —
        // never let it null out the existing value.
        if (! $this->filled('tracking_number')) {
            $this->request->remove('tracking_number');
        }
    }
}
