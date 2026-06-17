<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFirearmRequest extends FormRequest
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
            'label' => 'sometimes|required|string|max:255',
            'manufacturer' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'location_id' => 'nullable|integer|exists:cms.locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_store_id' => 'nullable|integer|exists:cms.stores,id',
            'calibers' => 'array',
            'calibers.*' => 'integer|exists:cms.calibers,id',
        ];
    }
}
