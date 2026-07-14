<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->user()->getKey();

        return [
            'store_id' => ['nullable', 'integer', Rule::exists(Store::class, 'id')->where('user_id', $userId)],
            'order_date' => ['required', 'date'],
            'order_ref' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ammunition_id' => ['required', 'integer', 'distinct', Rule::exists(Ammunition::class, 'id')->where('user_id', $userId)],
            'items.*.rounds' => ['required', 'integer', 'min:1'],
            'items.*.cost' => ['nullable', 'decimal:0,2', 'min:0'],
        ];
    }
}
