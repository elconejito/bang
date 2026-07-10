<?php

namespace App\Actions\Magazines;

use App\Models\Magazine;
use Illuminate\Support\Facades\DB;

final class ChangeMagazineState
{
    /**
     * @param  array{location_id: int|null, current_firearm_id: int|null, loaded_ammunition_id: int|null, loaded_rounds: int}  $state
     */
    public function handle(Magazine $magazine, array $state): Magazine
    {
        return DB::transaction(function () use ($magazine, $state): Magazine {
            $lockedMagazine = Magazine::query()->lockForUpdate()->findOrFail($magazine->getKey());
            $lockedMagazine->update($state);

            return $lockedMagazine->load(['calibers', 'compatibleFirearms', 'loadedAmmunition', 'location', 'currentFirearm']);
        });
    }
}
