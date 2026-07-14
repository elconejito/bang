<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'inventory_date' => Carbon::parse($this->inventory_date)->toDateString(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ammunition_id' => [
                'required',
                'integer',
                Rule::exists(Ammunition::class, 'id')->where('user_id', $this->user()->getKey()),
            ],
            'inventory_date' => ['required', 'date'],
            'rounds' => ['required', 'integer'],
            'is_purchase' => ['required', 'boolean'],
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
