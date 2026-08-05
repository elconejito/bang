<?php

namespace App\Actions\Magazines;

use App\Models\Ammunition;
use App\Models\Magazine;
use App\Models\User;
use App\Queries\Magazines\MagazineGroupQuery;
use App\Queries\Magazines\MagazinesInGroupQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class BulkUpdateMagazines
{
    public function __construct(
        private readonly MagazineGroupQuery $groups,
        private readonly MagazinesInGroupQuery $magazinesInGroup,
    ) {}

    /**
     * @param  list<int>  $magazineIds
     * @param  array<string, mixed>  $changes
     * @return array{updated_count: int, remaining_group_key: int|null, updated_group_key: int|null}
     */
    public function handle(User $user, int $groupId, array $magazineIds, array $changes): array
    {
        return DB::transaction(function () use ($user, $groupId, $magazineIds, $changes): array {
            $representative = Magazine::query()->with('calibers:id')->findOrFail($groupId);
            $sourceGroup = $this->groups->keyFor($representative);

            /** @var Collection<int, Magazine> $magazines */
            $magazines = Magazine::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->getKey())
                ->whereNull('archived_at')
                ->whereKey($magazineIds)
                ->with(['calibers:id', 'compatibleFirearms:id'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($magazines->count() !== count($magazineIds)) {
                throw ValidationException::withMessages([
                    'magazine_ids' => 'Every selected magazine must be active and belong to you.',
                ]);
            }

            foreach ($magazines as $magazine) {
                Gate::forUser($user)->authorize('update', $magazine);

                if ($this->groups->keyFor($magazine)->encode() !== $sourceGroup->encode()) {
                    throw ValidationException::withMessages([
                        'magazine_ids' => 'Every selected magazine must belong to this magazine group.',
                    ]);
                }
            }

            $this->validateFinalStates($user, $magazines, $changes);

            foreach ($magazines as $magazine) {
                $magazine->update($this->attributesFor($changes));

                if (array_key_exists('calibers', $changes)) {
                    $magazine->calibers()->sync($changes['calibers']);
                }

                if (array_key_exists('firearms', $changes)) {
                    $magazine->compatibleFirearms()->sync($changes['firearms']);
                }
            }

            $remainingGroupKey = $this->magazinesInGroup
                ->builder($user, $sourceGroup)
                ->min('id');
            $firstSelected = $magazines->firstWhere('id', $magazineIds[0]);
            $firstSelected->load('calibers:id');
            $updatedGroupKey = $this->magazinesInGroup
                ->builder($user, $this->groups->keyFor($firstSelected))
                ->min('id');

            return [
                'updated_count' => $magazines->count(),
                'remaining_group_key' => $remainingGroupKey === null ? null : (int) $remainingGroupKey,
                'updated_group_key' => $updatedGroupKey === null ? null : (int) $updatedGroupKey,
            ];
        });
    }

    /**
     * The first magazine ID in the request determines the representative when a result could span groups.
     *
     * @param  User  $user
     * @param  Collection<int, Magazine>  $magazines
     * @param  array<string, mixed>  $changes
     */
    private function validateFinalStates(User $user, Collection $magazines, array $changes): void
    {
        $hasContentsChange = array_key_exists('loaded_ammunition_id', $changes);
        $ammunition = $hasContentsChange && $changes['loaded_ammunition_id'] !== null
            ? Ammunition::query()->withoutGlobalScopes()->where('user_id', $user->getKey())->find($changes['loaded_ammunition_id'])
            : null;

        foreach ($magazines as $magazine) {
            $capacity = array_key_exists('capacity', $changes) ? $changes['capacity'] : $magazine->capacity;
            $caliberIds = array_key_exists('calibers', $changes) ? $changes['calibers'] : $magazine->calibers->modelKeys();
            $firearmIds = array_key_exists('firearms', $changes) ? $changes['firearms'] : $magazine->compatibleFirearms->modelKeys();
            $loadedAmmunitionId = $hasContentsChange ? $changes['loaded_ammunition_id'] : $magazine->loaded_ammunition_id;
            $loadedRounds = $hasContentsChange ? $changes['loaded_rounds'] : $magazine->loaded_rounds;

            if ($loadedRounds > $capacity) {
                throw ValidationException::withMessages([
                    'changes.capacity' => 'The final capacity may not be less than the loaded rounds of any selected magazine.',
                ]);
            }

            if (($loadedRounds === 0 && $loadedAmmunitionId !== null) || ($loadedRounds > 0 && $loadedAmmunitionId === null)) {
                throw ValidationException::withMessages([
                    'changes.loaded_ammunition_id' => 'An empty magazine cannot have loaded ammunition, and a loaded magazine requires ammunition.',
                ]);
            }

            if ($loadedAmmunitionId !== null) {
                $loadedAmmunition = $hasContentsChange
                    ? $ammunition
                    : Ammunition::query()->withoutGlobalScopes()->where('user_id', $user->getKey())->find($loadedAmmunitionId);

                if ($loadedAmmunition === null || ! in_array($loadedAmmunition->caliber_id, $caliberIds, true)) {
                    throw ValidationException::withMessages([
                        'changes.calibers' => 'The final calibers must include the caliber of every loaded ammunition selection.',
                    ]);
                }
            }

            if (! array_key_exists('location_id', $changes)
                && $magazine->current_firearm_id !== null
                && ! in_array($magazine->current_firearm_id, $firearmIds, true)) {
                throw ValidationException::withMessages([
                    'changes.firearms' => 'The final compatible firearms must include each firearm that currently contains a selected magazine.',
                ]);
            }
        }
    }

    /** @param array<string, mixed> $changes */
    private function attributesFor(array $changes): array
    {
        $attributes = [];

        foreach (['manufacturer', 'model_name', 'label', 'color_id', 'capacity'] as $field) {
            if (array_key_exists($field, $changes)) {
                $attributes[$field] = $changes[$field];
            }
        }

        if (array_key_exists('location_id', $changes)) {
            $attributes['location_id'] = $changes['location_id'];
            $attributes['current_firearm_id'] = null;
        }

        if (array_key_exists('loaded_ammunition_id', $changes)) {
            $attributes['loaded_ammunition_id'] = $changes['loaded_ammunition_id'];
            $attributes['loaded_rounds'] = $changes['loaded_rounds'];
        }

        return $attributes;
    }
}
