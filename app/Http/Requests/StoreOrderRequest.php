<?php

namespace App\Http\Requests;

use App\Enums\OrderAssetType;
use App\Models\Ammunition;
use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);
        if (is_array($items)) {
            $items = collect($items)->map(fn ($item) => is_array($item) ? ['type' => $item['type'] ?? 'ammunition', ...$item] : $item)->all();
        }
        $this->merge(['items' => $items]);
    }

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
            'items.*' => ['array'],
            'items.*.type' => ['required', 'string', Rule::in(['ammunition', ...array_keys(OrderAssetType::models())])],
            'items.*.ammunition_id' => ['required_if:items.*.type,ammunition', 'nullable', 'integer', 'distinct', Rule::exists(Ammunition::class, 'id')->where('user_id', $userId)],
            'items.*.asset_id' => ['required_unless:items.*.type,ammunition', 'nullable', 'integer'],
            'items.*.rounds' => ['required_if:items.*.type,ammunition', 'nullable', 'integer', 'min:1'],
            'items.*.cost' => ['nullable', 'decimal:0,2', 'min:0'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $seen = [];
            foreach ($this->input('items', []) as $index => $item) {
                if ($item['type'] !== 'ammunition') {
                    $key = $item['type'].':'.$item['asset_id'];
                    if (isset($seen[$key])) {
                        $validator->errors()->add("items.$index.asset_id", 'Each asset may only appear once.');
                    }
                    $seen[$key] = true;
                }
            }
        }];
    }
}
