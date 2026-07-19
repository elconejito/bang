<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BatchMountAccessoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'accessories' => ['required', 'array', 'min:1', 'max:50'],
            'accessories.*.type' => ['required', 'string', Rule::in(['Suppressor', 'Optic', 'Light', 'Misc'])],
            'accessories.*.id' => ['required', 'integer'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $keys = collect($this->input('accessories', []))
                ->map(fn (array $accessory): string => ($accessory['type'] ?? '').':'.($accessory['id'] ?? ''));

            if ($keys->count() !== $keys->unique()->count()) {
                $validator->errors()->add('accessories', 'Each accessory may only be selected once.');
            }
        }];
    }
}
