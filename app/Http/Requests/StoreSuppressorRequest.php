<?php

namespace App\Http\Requests;

use App\Models\Reference\Color;
use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSuppressorRequest extends FormRequest
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
            'model_number' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'color_id' => ['nullable', 'integer', Rule::exists(Color::class, 'id')],
            'caliber_id' => 'nullable|integer|exists:calibers,id',
            'is_nfa' => 'boolean',
            'mount_type' => 'nullable|string|max:255',
            'length' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'nfa_form_type' => 'nullable|string|max:255',
            'nfa_approved_date' => 'nullable|date',
            'nfa_trust' => 'nullable|string|max:255',
            'firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'location_id' => 'nullable|integer|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_store_id' => 'nullable|integer|exists:stores,id',
        ];
    }
}
