<?php

namespace App\Http\Requests;

use App\Models\Firearm;
use App\Models\Location;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexMagazinesInGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array'],
            'filter.compatible_firearm_id' => ['nullable', 'integer', Rule::exists(Firearm::class, 'id')->where(fn (Builder $query): Builder => $query->where('user_id', $this->user()->id))],
            'filter.state' => ['nullable', Rule::in(['in_gun', 'loaded', 'empty'])],
            'filter.location_id' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (in_array($value, ['in_firearm', 'unassigned'], true)) {
                    return;
                }

                if (! is_numeric($value) || ! Location::query()->withoutGlobalScopes()->where('user_id', $this->user()->id)->whereKey((int) $value)->exists()) {
                    $fail('The selected location is invalid.');
                }
            }],
            'filter.search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['id_marking', '-id_marking', 'state', '-state', 'loaded_ammunition', '-loaded_ammunition', 'location', '-location'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
