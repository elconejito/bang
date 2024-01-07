<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

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
            'ammunition_id'  => 'required|integer',
            'inventory_date' => 'required|date',
            'rounds'         => 'required|integer',
            'is_purchase'    => 'required|boolean',
            'cost'           => 'required_if:is_purchase,true|decimal:0,2|nullable',
            'store_id'       => 'required_if:is_purchase,true|integer|nullable',
        ];
    }
}