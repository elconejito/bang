<?php

namespace App\Http\Requests;

use App\Models\Reference\BulletType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBulletTypeRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'label' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(BulletType::class, 'label')->ignore($this->route('bullet_type')),
            ],
            'abbreviation' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
