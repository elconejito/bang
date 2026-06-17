<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmmunitionRequest extends FormRequest
{
    /**
     * @return bool
     */
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
            'caliber_id' => 'sometimes|required|integer|exists:calibers,id',
            'manufacturer' => 'sometimes|required|string|max:255',
            'label' => 'sometimes|required|string|max:255',
            'purpose_id' => 'nullable|integer|exists:purposes,id',
            'weight' => 'nullable|integer',
            'shell_length_id' => 'nullable|integer',
            'shell_type_id' => 'nullable|integer',
            'shot_material_id' => 'nullable|integer',
            'ammunition_casing_id' => 'nullable|integer',
            'ammunition_condition_id' => 'nullable|integer',
            'bullet_type_id' => 'nullable|integer',
            'primer_type_id' => 'nullable|integer',
        ];
    }
}
