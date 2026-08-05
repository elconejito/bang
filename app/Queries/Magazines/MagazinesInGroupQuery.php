<?php

namespace App\Queries\Magazines;

use App\Data\Magazines\MagazineGroupKey;
use App\Models\Ammunition;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class MagazinesInGroupQuery
{
    public function builder(User $user, MagazineGroupKey $group, string $lifecycleStatus = 'active'): Builder
    {
        $query = Magazine::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->getKey())
            ->when($lifecycleStatus === 'active', fn (Builder $query): Builder => $query->whereNull('archived_at'))
            ->when($lifecycleStatus === 'archived', fn (Builder $query): Builder => $query->whereNotNull('archived_at'))
            ->whereRaw($this->normalizedColumn('manufacturer').' = ?', [$group->manufacturer])
            ->where('capacity', $group->capacity)
            ->with(['calibers:id,label', 'color:id,label', 'loadedAmmunition:id,manufacturer,label', 'location:id,label', 'currentFirearm:id,label,manufacturer']);

        if ($group->modelName === null) {
            $query->whereNull('model_name');
        } else {
            $query->whereRaw($this->normalizedColumn('model_name').' = ?', [$group->modelName]);
        }

        return $query
            ->whereRaw('(select count(*) from cms.caliber_magazine where magazine_id = cms.magazines.id) = ?', [count($group->caliberIds)])
            ->when(
                $group->caliberIds !== [],
                fn (Builder $query): Builder => $query->whereNotExists(function ($query) use ($group): void {
                    $query->selectRaw('1')
                        ->from('cms.caliber_magazine')
                        ->whereColumn('magazine_id', 'cms.magazines.id')
                        ->whereNotIn('caliber_id', $group->caliberIds);
                }),
            );
    }

    /** @param array{compatible_firearm_id?: int, state?: string, location_id?: int|string, search?: string, sort?: string, per_page?: int} $parameters */
    public function paginate(User $user, MagazineGroupKey $group, array $parameters): LengthAwarePaginator
    {
        $query = $this->builder($user, $group, $parameters['lifecycle_status'] ?? 'active')
            ->when($parameters['compatible_firearm_id'] ?? null, fn (Builder $query, int $firearmId): Builder => $query->compatibleWithFirearm($firearmId))
            ->when($parameters['search'] ?? null, fn (Builder $query, string $search): Builder => $query->whereLike('id_marking', "%{$search}%", caseSensitive: false));

        $this->applyStateFilter($query, $parameters['state'] ?? null);
        $this->applyLocationFilter($query, $parameters['location_id'] ?? null);
        $this->applySort($query, $parameters['sort'] ?? 'id_marking');

        return $query->paginate(min(max((int) ($parameters['per_page'] ?? 25), 1), 100));
    }

    private function applyStateFilter(Builder $query, ?string $state): void
    {
        match ($state) {
            'in_gun' => $query->whereNotNull('current_firearm_id'),
            'loaded' => $query->whereNull('current_firearm_id')->where('loaded_rounds', '>', 0),
            'empty' => $query->whereNull('current_firearm_id')->where('loaded_rounds', 0),
            default => null,
        };
    }

    private function applyLocationFilter(Builder $query, int|string|null $location): void
    {
        match ($location) {
            'in_firearm' => $query->whereNotNull('current_firearm_id'),
            'unassigned' => $query->whereNull('current_firearm_id')->whereNull('location_id'),
            null => null,
            default => $query->whereNull('current_firearm_id')->where('location_id', (int) $location),
        };
    }

    private function applySort(Builder $query, string $sort): void
    {
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

        match (ltrim($sort, '-')) {
            'state' => $query->orderByRaw("case when current_firearm_id is not null then 1 when loaded_rounds > 0 then 2 else 3 end {$direction}"),
            'loaded_ammunition' => $query->orderBy(
                Ammunition::query()->selectRaw("lower(manufacturer || ' ' || label)")->whereColumn('id', 'cms.magazines.loaded_ammunition_id')->limit(1),
                $direction,
            ),
            'location' => $query->orderByRaw("lower(coalesce((select manufacturer || ' ' || label from cms.firearms where id = cms.magazines.current_firearm_id), (select label from cms.locations where id = cms.magazines.location_id), 'Unassigned')) {$direction}"),
            default => $query->orderByRaw("lower(coalesce(id_marking, '')) {$direction}"),
        };

        $query->orderBy('id');
    }

    private function normalizedColumn(string $column): string
    {
        return "lower(regexp_replace(btrim({$column}), '\\s+', ' ', 'g'))";
    }
}
