<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFirearmActivityEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', Rule::in(['CLEAN', 'REPAIR'])],
            'event_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
