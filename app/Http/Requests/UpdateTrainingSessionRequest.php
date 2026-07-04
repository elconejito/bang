<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingSessionRequest extends FormRequest
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
            'label' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'session_date' => 'sometimes|required|date',
            'range_id' => 'nullable|integer|exists:ranges,id',
        ];
    }
}
