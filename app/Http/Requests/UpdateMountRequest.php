<?php

namespace App\Http\Requests;

class UpdateMountRequest extends StoreMountRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'manufacturer' => ['sometimes', 'required', 'string', 'max:255'],
            'label' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
