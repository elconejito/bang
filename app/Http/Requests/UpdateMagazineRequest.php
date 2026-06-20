<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMagazineRequest extends FormRequest
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
            'manufacturer' => 'sometimes|required|string|max:255',
            'label' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
            'capacity' => 'sometimes|required|integer|min:1',
            'serial_number' => 'nullable|string|max:255',
            'id_marking' => 'nullable|string|max:255',
            'status' => 'nullable|in:empty,loaded,in_gun',
            'loaded_ammunition_id' => 'nullable|integer|exists:ammunition,id',
            'calibers' => 'array',
            'calibers.*' => 'integer|exists:calibers,id',
            'firearms' => 'array',
            'firearms.*' => 'integer|exists:firearms,id',
        ];
    }
}
