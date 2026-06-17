<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'manufacturer' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'optic_type' => 'nullable|string|max:255',
            'battery_type' => 'nullable|string|max:255',
            'firearm_id' => 'nullable|integer|exists:firearms,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_store_id' => 'nullable|integer|exists:stores,id',
        ];
    }
}
