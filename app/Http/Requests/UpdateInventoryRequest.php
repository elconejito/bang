<?php

namespace App\Http\Requests;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'inventory_date' => ['required', 'date'],
            'rounds' => ['required', 'integer', 'not_in:0'],
            'cost' => ['nullable', 'decimal:0,2', 'min:0'],
            'store_id' => [
                'nullable',
                'integer',
                Rule::exists(Store::class, 'id')->where('user_id', $this->user()->getKey()),
            ],
            'order_ref' => ['nullable', 'string', 'max:255'],
        ];
    }
}
