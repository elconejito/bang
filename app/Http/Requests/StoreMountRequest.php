<?php

namespace App\Http\Requests;

use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'manufacturer' => ['required', 'string', 'max:255'], 'label' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'], 'height' => ['nullable', 'string', 'max:255'],
            'mount_type' => ['nullable', Rule::in(['picatinny', 'mlok', 'keymod'])],
            'color_id' => ['nullable', 'integer', 'exists:reference.colors,id'],
            'firearm_id' => ['nullable', 'integer', new ActiveFirearm($this->user()->id)],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'], 'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'], 'purchase_store_id' => ['nullable', 'integer', 'exists:stores,id'],
        ];
    }
}
