<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFirearmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'customizer' => 'nullable|string|max:255',
            'custom_package' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'location_id' => 'nullable|integer|exists:locations,id',
            'color_id' => 'nullable|integer|exists:reference.colors,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_store_id' => 'nullable|integer|exists:stores,id',
            'calibers' => 'array',
            'calibers.*' => 'integer|exists:calibers,id',
        ];
    }
}
