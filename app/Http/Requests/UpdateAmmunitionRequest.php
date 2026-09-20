<?php

namespace App\Http\Requests;

use App\Models\Reference\ShellLength;
use App\Models\Reference\ShellType;
use App\Models\Reference\ShotMaterial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'caliber_id' => 'sometimes|required|integer|exists:calibers,id',
            'manufacturer' => 'sometimes|required|string|max:255',
            'label' => 'sometimes|required|string|max:255',
            'purpose_id' => 'nullable|integer|exists:purposes,id',
            'weight' => 'nullable|integer',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_target' => 'nullable|integer|min:0',
            'shell_length_id' => ['nullable', 'integer', Rule::exists(ShellLength::class, 'id')],
            'shell_type_id' => ['nullable', 'integer', Rule::exists(ShellType::class, 'id')],
            'shot_material_id' => ['nullable', 'integer', Rule::exists(ShotMaterial::class, 'id')],
            'ammunition_casing_id' => 'nullable|integer',
            'ammunition_condition_id' => 'nullable|integer',
            'bullet_type_id' => 'nullable|integer',
            'primer_type_id' => 'nullable|integer',
        ];
    }
}
