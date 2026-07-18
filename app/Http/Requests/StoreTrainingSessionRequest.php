<?php

namespace App\Http\Requests;

use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingSessionRequest extends FormRequest
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
            'label' => 'required|string',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'range_id' => 'nullable|integer|exists:ranges,id',
            'lines' => 'nullable|array',
            'lines.*.firearm_id' => ['required', 'integer', new ActiveFirearm($this->user()->id)],
            'lines.*.ammunition_id' => 'required|integer|exists:ammunition,id',
            'lines.*.suppressor_id' => 'nullable|integer|exists:suppressors,id',
            'lines.*.rounds' => 'required|integer|min:1',
            'lines.*.deduct_ammo' => 'boolean',
            'lines.*.add_firearm_count' => 'boolean',
            'lines.*.add_suppressor_count' => 'boolean',
        ];
    }
}
