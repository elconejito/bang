<?php

namespace App\Http\Requests;

use App\Enums\ArchiveReason;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArchiveFirearmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::enum(ArchiveReason::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'unmount_all_accessories' => ['sometimes', 'boolean'],
            'unmount_accessories' => ['sometimes', 'array', 'prohibited_if:unmount_all_accessories,true'],
            'unmount_accessories.*.type' => ['required', Rule::in(['suppressor', 'optic', 'light', 'misc_accessory'])],
            'unmount_accessories.*.id' => ['required', 'integer', 'min:1'],
        ];
    }
}
