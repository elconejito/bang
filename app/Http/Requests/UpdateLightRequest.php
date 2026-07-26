<?php

namespace App\Http\Requests;

use App\Models\Reference\Color;
use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLightRequest extends FormRequest
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
            'manufacturer' => 'sometimes|required|string|max:255',
            'label' => 'sometimes|required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'color_id' => ['nullable', 'integer', Rule::exists(Color::class, 'id')],
            'lumens' => 'nullable|integer|min:0',
            'battery_type' => 'nullable|string|max:255',
            'laser' => 'nullable|in:red,green,ir',
            'beam_pattern' => 'nullable|in:flood,throw,mixed',
            'firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'location_id' => 'nullable|integer|exists:locations,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_store_id' => 'nullable|integer|exists:stores,id',
        ];
    }
}
