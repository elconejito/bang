<?php

namespace App\Http\Requests;

use App\Models\Caliber;
use App\Models\Firearm;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexMagazineGroupsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array'],
            'filter.search' => ['nullable', 'string', 'max:100'],
            'filter.compatible_firearm_id' => ['nullable', 'integer', Rule::exists(Firearm::class, 'id')->where(fn (Builder $query): Builder => $query->where('user_id', $this->user()->id))],
            'filter.caliber_id' => ['nullable', 'integer', Rule::exists(Caliber::class, 'id')->where(fn (Builder $query): Builder => $query->where('user_id', $this->user()->id))],
            'sort' => ['nullable', Rule::in(['manufacturer', '-manufacturer', 'model_name', '-model_name', 'capacity', '-capacity', '-total', '-loaded_count'])],
        ];
    }
}
