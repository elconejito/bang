<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location_type_id' => ['nullable', 'integer'],
            'parent_location_id' => [
                'nullable',
                'integer',
                Rule::exists(Location::class, 'id')
                    ->where(fn (Builder $query): Builder => $query->where('user_id', $userId)),
            ],
        ];
    }
}
