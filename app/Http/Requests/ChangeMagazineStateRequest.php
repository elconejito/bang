<?php

namespace App\Http\Requests;

use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ChangeMagazineStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $magazine = $this->route('magazine');

        return $magazine instanceof Magazine && $this->user()?->can('update', $magazine) === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->user()->getKey();

        return [
            'location_id' => ['present', 'nullable', 'integer', Rule::exists(Location::class, 'id')->where('user_id', $userId)],
            'current_firearm_id' => ['present', 'nullable', 'integer', Rule::exists(Firearm::class, 'id')->where('user_id', $userId)],
            'loaded_ammunition_id' => ['present', 'nullable', 'integer', Rule::exists(Ammunition::class, 'id')->where('user_id', $userId)],
            'loaded_rounds' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Magazine $magazine */
            $magazine = $this->route('magazine');
            $rounds = $this->integer('loaded_rounds');
            $ammunitionId = $this->input('loaded_ammunition_id');
            $firearmId = $this->input('current_firearm_id');

            if ($rounds > $magazine->capacity) {
                $validator->errors()->add('loaded_rounds', 'The loaded rounds may not exceed the magazine capacity.');
            }

            if ($this->filled('location_id') && $this->filled('current_firearm_id')) {
                $validator->errors()->add('location_id', 'A magazine cannot be stored in a location and inserted into a firearm at the same time.');
            }

            if ($rounds > 0 && $ammunitionId === null) {
                $validator->errors()->add('loaded_ammunition_id', 'Loaded ammunition is required when the magazine contains rounds.');
            }

            if ($rounds === 0 && $ammunitionId !== null) {
                $validator->errors()->add('loaded_ammunition_id', 'An empty magazine cannot have loaded ammunition.');
            }

            if ($firearmId !== null && ! $magazine->compatibleFirearms()->whereKey($firearmId)->exists()) {
                $validator->errors()->add('current_firearm_id', 'The magazine is not compatible with the selected firearm.');
            }

            if ($firearmId !== null && Magazine::query()
                ->where('current_firearm_id', $firearmId)
                ->whereKeyNot($magazine->getKey())
                ->exists()) {
                $validator->errors()->add('current_firearm_id', 'The selected firearm already has a magazine inserted.');
            }

            if ($ammunitionId !== null) {
                $ammunition = Ammunition::query()->find($ammunitionId);
                if ($ammunition !== null && ! $magazine->calibers()->whereKey($ammunition->caliber_id)->exists()) {
                    $validator->errors()->add('loaded_ammunition_id', 'The ammunition caliber is not compatible with this magazine.');
                }
            }
        }];
    }
}
