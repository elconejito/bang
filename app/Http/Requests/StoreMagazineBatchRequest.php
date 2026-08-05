<?php

namespace App\Http\Requests;

use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Reference\Color;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMagazineBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->user()->getKey();

        return [
            'manufacturer' => ['required', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:255'],
            'model_name' => ['nullable', 'string', 'max:255'],
            'color_id' => ['nullable', 'integer', Rule::exists(Color::class, 'id')],
            'capacity' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'marking_prefix' => ['nullable', 'string', 'max:245', 'required_with:marking_start,marking_width'],
            'marking_start' => ['nullable', 'required_with:marking_prefix', 'integer', 'min:0', 'max:9999999900'],
            'marking_width' => ['nullable', 'required_with:marking_prefix', 'integer', 'min:1', 'max:10'],
            'calibers' => ['sometimes', 'array'],
            'calibers.*' => ['integer', 'distinct', Rule::exists(Caliber::class, 'id')->where('user_id', $userId)],
            'firearms' => ['sometimes', 'array'],
            'firearms.*' => ['integer', 'distinct', Rule::exists(Firearm::class, 'id')->where('user_id', $userId)],
            'location_id' => ['nullable', 'integer', Rule::exists(Location::class, 'id')->where('user_id', $userId)],
        ];
    }
}
