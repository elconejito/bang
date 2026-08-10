<?php

namespace App\Actions\Magazines;

use App\Data\Magazines\MagazineGroupKey;
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
     * @return array{
     *     updated_count: int,
     *     remaining_group_key: int|null,
     *     updated_group_key: int|null,
     *     remaining_group: array{key: int, count: int, manufacturer: string, model_name: string|null, model_number: string|null, capacity: int, calibers: list<array{id: int, label: string}>}|null,
     *     updated_group: array{key: int, count: int, manufacturer: string, model_name: string|null, model_number: string|null, capacity: int, calibers: list<array{id: int, label: string}>}|null
     * }
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

            if (! $this->hasChanges($magazines, $changes)) {
                throw ValidationException::withMessages([
                    'changes' => 'No selected magazine would change.',
                ]);
            }

            foreach ($magazines as $magazine) {
                $magazine->update($this->attributesFor($changes));

                if (array_key_exists('calibers', $changes)) {
                    $magazine->calibers()->sync($changes['calibers']);
                }

                if (array_key_exists('firearms', $changes)) {
                    $magazine->compatibleFirearms()->sync($changes['firearms']);
                }
            }

            $remainingGroup = $this->groupSummary($user, $sourceGroup);
            $firstSelected = Magazine::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->getKey())
                ->with('calibers:id,label')
                ->findOrFail($magazineIds[0]);
            $updatedGroup = $this->groupSummary($user, $this->groups->keyFor($firstSelected));

            return [
                'updated_count' => $magazines->count(),
                'remaining_group_key' => $remainingGroup['key'] ?? null,
                'updated_group_key' => $updatedGroup['key'] ?? null,
                'remaining_group' => $remainingGroup,
                'updated_group' => $updatedGroup,
            ];
        });
    }

    /**
     * @param  Collection<int, Magazine>  $magazines
     * @param  array<string, mixed>  $changes
     */
    private function hasChanges(Collection $magazines, array $changes): bool
    {
        return $magazines->contains(fn (Magazine $magazine): bool => $this->magazineWouldChange($magazine, $changes));
    }

    /**
     * @param  array<string, mixed>  $changes
     */
    private function magazineWouldChange(Magazine $magazine, array $changes): bool
    {
        foreach ($this->attributesFor($changes) as $field => $value) {
            if (! $this->sameAttributeValue($magazine, $field, $value)) {
                return true;
            }
        }

        if (array_key_exists('calibers', $changes) && ! $this->sameIds($magazine->calibers->modelKeys(), $changes['calibers'])) {
            return true;
        }

        return array_key_exists('firearms', $changes)
            && ! $this->sameIds($magazine->compatibleFirearms->modelKeys(), $changes['firearms']);
    }

    private function sameAttributeValue(Magazine $magazine, string $field, mixed $value): bool
    {
        if (in_array($field, ['color_id', 'capacity', 'location_id', 'current_firearm_id', 'loaded_ammunition_id', 'loaded_rounds'], true)) {
            return $magazine->getAttribute($field) === ($value === null ? null : (int) $value);
        }

        return $magazine->getAttribute($field) === $value;
    }

    /**
     * @param  list<int>  $first
     * @param  list<int>  $second
     */
    private function sameIds(array $first, array $second): bool
    {
        $first = array_map('intval', $first);
        $second = array_map('intval', $second);

        sort($first, SORT_NUMERIC);
        sort($second, SORT_NUMERIC);

        return $first === $second;
    }

    /**
     * @return array{key: int, count: int, manufacturer: string, model_name: string|null, model_number: string|null, capacity: int, calibers: list<array{id: int, label: string}>}|null
     */
    private function groupSummary(User $user, MagazineGroupKey $group): ?array
    {
        $representativeId = $this->magazinesInGroup->builder($user, $group)->min('id');

        if ($representativeId === null) {
            return null;
        }

        $representative = Magazine::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->getKey())
            ->with('calibers:id,label')
            ->findOrFail($representativeId);

        return [
            'key' => (int) $representativeId,
            'count' => $this->magazinesInGroup->builder($user, $group)->count(),
            'manufacturer' => $representative->manufacturer,
            'model_name' => $representative->model_name,
            'model_number' => $representative->model_number,
            'capacity' => $representative->capacity,
            'calibers' => $representative->calibers->map(fn ($caliber): array => ['id' => $caliber->id, 'label' => $caliber->label])->values()->all(),
        ];
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
            $caliberIds = array_map('intval', array_key_exists('calibers', $changes) ? $changes['calibers'] : $magazine->calibers->modelKeys());
            $firearmIds = array_map('intval', array_key_exists('firearms', $changes) ? $changes['firearms'] : $magazine->compatibleFirearms->modelKeys());
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

        foreach (['manufacturer', 'model_name', 'model_number', 'label', 'color_id', 'capacity'] as $field) {
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
