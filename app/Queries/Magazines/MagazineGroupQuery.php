<?php

namespace App\Queries\Magazines;

use App\Data\Magazines\MagazineGroupKey;
use App\Models\Firearm;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class MagazineGroupQuery
{
    /**
     * @return Collection<int, array{key: MagazineGroupKey, magazines: Collection<int, Magazine>}>
     */
    public function get(User $user, ?Firearm $compatibleFirearm = null, ?string $search = null, ?int $caliberId = null, string $sort = 'manufacturer'): Collection
    {
        $groups = $this->baseQuery($user, $compatibleFirearm)
            ->when($search, fn (Builder $query, string $search): Builder => $query->where(function (Builder $query) use ($search): void {
                $query->whereLike('manufacturer', "%{$search}%", caseSensitive: false)
                    ->orWhereLike('model_name', "%{$search}%", caseSensitive: false)
                    ->orWhereLike('id_marking', "%{$search}%", caseSensitive: false);
            }))
            ->when($caliberId, fn (Builder $query, int $caliberId): Builder => $query->whereHas('calibers', fn (Builder $query): Builder => $query->whereKey($caliberId)))
            ->get()
            ->groupBy(fn (Magazine $magazine): string => $this->keyFor($magazine)->encode())
            ->map(fn (Collection $magazines): array => [
                'key' => $this->keyFor($magazines->first()),
                'magazines' => $magazines->values(),
            ])
            ->values();

        $descending = str_starts_with($sort, '-');
        $field = ltrim($sort, '-');

        return $groups->sortBy(function (array $group) use ($field): string|int {
            /** @var Magazine $magazine */
            $magazine = $group['magazines']->first();

            return match ($field) {
                'model_name' => mb_strtolower($magazine->model_name ?? ''),
                'capacity' => $magazine->capacity,
                'total' => $group['magazines']->count(),
                'loaded_count' => $group['magazines']->where('loaded_rounds', '>', 0)->count(),
                default => mb_strtolower($magazine->manufacturer),
            };
        }, descending: $descending)->values();
    }

    public function baseQuery(User $user, ?Firearm $compatibleFirearm = null): Builder
    {
        return Magazine::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->getKey())
            ->when(
                $compatibleFirearm !== null,
                fn (Builder $query): Builder => $query->compatibleWithFirearm($compatibleFirearm),
            )
            ->with(['calibers:id,label', 'location:id,label', 'currentFirearm:id,label,manufacturer']);
    }

    public function keyFor(Magazine $magazine): MagazineGroupKey
    {
        $magazine->loadMissing('calibers:id');

        return MagazineGroupKey::make(
            $magazine->manufacturer,
            $magazine->model_name,
            $magazine->capacity,
            $magazine->calibers->modelKeys(),
        );
    }
}
