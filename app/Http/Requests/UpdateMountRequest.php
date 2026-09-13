<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Reference\Color;
use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'manufacturer' => ['sometimes', 'required', 'string', 'max:255'],
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'model_number' => ['nullable', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'height' => ['nullable', 'string', 'max:255'],
            'mount_type' => ['nullable', Rule::in(['picatinny', 'mlok', 'keymod'])],
            'color_id' => ['nullable', 'integer', Rule::exists(Color::class, 'id')],
            'firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'order_id' => ['nullable', 'integer', Rule::exists(Order::class, 'id')->where('user_id', $this->user()->id)],
            'cost' => ['nullable', 'decimal:0,2', 'min:0'],
        ];
    }
}
