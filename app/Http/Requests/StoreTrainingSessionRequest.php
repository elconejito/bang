<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Rules\ActiveFirearm;
use App\Rules\ActiveSuppressor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingSessionRequest extends FormRequest
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
            'label' => 'required|string',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'range_id' => 'nullable|integer|exists:ranges,id',
            'lines' => 'nullable|array',
            'lines.*.firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'lines.*.ammunition_id' => ['required', 'integer', Rule::exists(Ammunition::class, 'id')->where('user_id', $this->user()->getKey())],
            'lines.*.suppressor_id' => ['nullable', 'integer', new ActiveSuppressor($this->user()->id)],
            'lines.*.rounds' => 'required|integer|min:1',
            'lines.*.deduct_ammo' => 'boolean',
            'lines.*.add_firearm_count' => 'boolean',
            'lines.*.add_suppressor_count' => 'boolean',
        ];
    }
}
