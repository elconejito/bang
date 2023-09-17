<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'label'                       => 'string|required',
            'description'                 => 'string|nullable',
            'session_date'                => 'date|required',
            'location_id'                 => 'integer|nullable',
            'inventories'                 => 'array:ammunition_id,rounds|nullable',
            'inventories.*.ammunition_id' => 'required|integer',
            'inventories.*.rounds'        => 'required|integer',
        ];
    }
}
