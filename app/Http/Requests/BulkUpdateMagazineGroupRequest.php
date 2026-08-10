<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\Reference\Color;
use App\Rules\ActiveFirearm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BulkUpdateMagazineGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Magazine::class) === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->user()->getKey();

        return [
            'magazine_ids' => ['required', 'array', 'min:1'],
            'magazine_ids.*' => ['required', 'integer', 'distinct'],
            'changes' => ['required', 'array:manufacturer,model_name,model_number,label,color_id,capacity,calibers,firearms,location_id,loaded_ammunition_id,loaded_rounds,current_firearm_id', 'min:1'],
            'changes.manufacturer' => ['sometimes', 'required', 'string', 'max:255'],
            'changes.model_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'changes.model_number' => ['sometimes', 'nullable', 'string', 'max:255'],
            'changes.label' => ['sometimes', 'nullable', 'string', 'max:255'],
            'changes.color_id' => ['sometimes', 'nullable', 'integer', Rule::exists(Color::class, 'id')->where('user_id', $userId)],
            'changes.capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'changes.calibers' => ['sometimes', 'array'],
            'changes.calibers.*' => ['integer', 'distinct', Rule::exists(Caliber::class, 'id')->where('user_id', $userId)],
            'changes.firearms' => ['sometimes', 'array'],
            'changes.firearms.*' => ['integer', 'distinct', new ActiveFirearm($userId)],
            'changes.location_id' => ['sometimes', 'nullable', 'integer', Rule::exists(Location::class, 'id')->where('user_id', $userId)],
            'changes.loaded_ammunition_id' => ['sometimes', 'nullable', 'integer', Rule::exists(Ammunition::class, 'id')->where('user_id', $userId)],
            'changes.loaded_rounds' => ['sometimes', 'integer', 'min:0'],
            'changes.current_firearm_id' => ['missing'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $hasAmmunition = $this->exists('changes.loaded_ammunition_id');
            $hasRounds = $this->exists('changes.loaded_rounds');

            if ($hasAmmunition === $hasRounds) {
                return;
            }

            $validator->errors()->add('changes.loaded_ammunition_id', 'Loaded ammunition and loaded rounds must be changed together.');
            $validator->errors()->add('changes.loaded_rounds', 'Loaded ammunition and loaded rounds must be changed together.');
        }];
    }
}
