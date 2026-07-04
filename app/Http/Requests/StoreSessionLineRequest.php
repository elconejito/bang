<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionLineRequest extends FormRequest
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
            'firearm_id' => 'required|integer|exists:firearms,id',
            'ammunition_id' => 'required|integer|exists:ammunition,id',
            'suppressor_id' => 'nullable|integer|exists:suppressors,id',
            'rounds' => 'required|integer|min:1',
            'deduct_ammo' => 'boolean',
            'add_firearm_count' => 'boolean',
            'add_suppressor_count' => 'boolean',
        ];
    }
}
