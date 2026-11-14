<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Rules\ActiveFirearm;
use App\Rules\ActiveSuppressor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionLineRequest extends FormRequest
{
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
            'firearm_id' => ['sometimes', 'nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'ammunition_id' => ['sometimes', 'required', 'integer', Rule::exists(Ammunition::class, 'id')->where('user_id', $this->user()->getKey())],
            'suppressor_id' => ['nullable', 'integer', new ActiveSuppressor($this->user()->id)],
            'rounds' => 'sometimes|required|integer|min:1',
            'deduct_ammo' => 'boolean',
            'add_firearm_count' => 'boolean',
            'add_suppressor_count' => 'boolean',
        ];
    }
}
