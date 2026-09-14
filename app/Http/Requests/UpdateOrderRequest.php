<?php

namespace App\Http\Requests;

class UpdateOrderRequest extends StoreOrderRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'items.*.id' => ['nullable', 'integer'],
        ];
    }
}
