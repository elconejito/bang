<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'location_type_id' => ['sometimes', 'nullable', 'integer'],
            'parent_location_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(Location::class, 'id')
                    ->where(fn (Builder $query): Builder => $query->where('user_id', $userId)),
            ],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (
                    $validator->errors()->has('parent_location_id')
                    || ! $this->filled('parent_location_id')
                ) {
                    return;
                }

                /** @var Location $location */
                $location = $this->route('location');
                $parent = Location::query()->find($this->integer('parent_location_id'));

                if ($parent !== null && $location->wouldCreateCycleWith($parent)) {
                    $validator->errors()->add(
                        'parent_location_id',
                        'A location cannot be placed inside itself or one of its sublocations.'
                    );
                }
            },
        ];
    }
}
