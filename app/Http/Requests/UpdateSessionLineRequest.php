<?php

namespace App\Http\Requests;

use App\Rules\ActiveFirearm;
use App\Rules\ActiveSuppressor;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionLineRequest extends FormRequest
{
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
            'firearm_id' => ['sometimes', 'required', 'integer', new ActiveFirearm($this->user()->id)],
            'ammunition_id' => 'sometimes|required|integer|exists:ammunition,id',
            'suppressor_id' => ['nullable', 'integer', new ActiveSuppressor($this->user()->id)],
            'rounds' => 'sometimes|required|integer|min:1',
            'deduct_ammo' => 'boolean',
            'add_firearm_count' => 'boolean',
            'add_suppressor_count' => 'boolean',
        ];
    }
}
