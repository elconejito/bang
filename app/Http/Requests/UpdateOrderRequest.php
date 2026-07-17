<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends StoreOrderRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'items.*.id' => [
                'nullable',
                'integer',
                Rule::exists(Inventory::class, 'id')->where('order_id', $this->route('order')->getKey()),
            ],
        ];
    }
}
