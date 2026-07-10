<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMagazineRequest extends FormRequest
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
            'manufacturer' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'serial_number' => 'nullable|string|max:255',
            'id_marking' => 'nullable|string|max:255',
            'loaded_ammunition_id' => 'nullable|integer|exists:ammunition,id',
            'calibers' => 'array',
            'calibers.*' => 'integer|exists:calibers,id',
            'firearms' => 'array',
            'firearms.*' => 'integer|exists:firearms,id',
        ];
    }
}
