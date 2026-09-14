<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Reference\Color;
use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLightRequest extends FormRequest
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
            'lumens' => 'nullable|integer|min:0',
            'battery_type' => 'nullable|string|max:255',
            'laser' => 'nullable|in:red,green,ir',
            'beam_pattern' => 'nullable|in:flood,throw,mixed',
            'firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'location_id' => 'nullable|integer|exists:locations,id',
            'order_id' => ['nullable', 'integer', Rule::exists(Order::class, 'id')->where('user_id', $this->user()->id)],
            'cost' => ['nullable', 'decimal:0,2', 'min:0'],
        ];
    }
}
