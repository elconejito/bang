<?php

namespace App\Actions\Firearms;

use App\Enums\ArchiveReason;
use App\Models\Firearm;
use Illuminate\Support\Facades\DB;

class ArchiveFirearm
{
    /**
     * @param  array<int, array{type: string, id: int}>  $selectedAccessories
     */
    public function execute(Firearm $firearm, ArchiveReason $reason, ?string $description, int $actorId, bool $unmountAllAccessories = false, array $selectedAccessories = []): Firearm
    {
        return DB::transaction(function () use ($firearm, $reason, $description, $actorId, $unmountAllAccessories, $selectedAccessories): Firearm {
            $lockedFirearm = Firearm::query()->whereKey($firearm)->lockForUpdate()->firstOrFail();

            if ($lockedFirearm->isArchived()) {
                return $lockedFirearm;
            }

            $this->unmountAccessories($lockedFirearm, $unmountAllAccessories, $selectedAccessories);
            $lockedFirearm->update(['archived_at' => now(), 'archive_reason' => $reason, 'archive_description' => $description]);
            $lockedFirearm->activityEvents()->create([
                'user_id' => $lockedFirearm->user_id,
                'actor_id' => $actorId,
                'type' => 'ARCHIVED',
                'occurred_at' => now(),
                'description' => $description,
                'metadata' => ['reason' => $reason->value],
            ]);

            return $lockedFirearm->refresh();
        }, attempts: 3);
    }

    /** @param array<int, array{type: string, id: int}> $selectedAccessories */
    private function unmountAccessories(Firearm $firearm, bool $all, array $selectedAccessories): void
    {
        $relations = ['suppressor' => 'suppressors', 'optic' => 'optics', 'light' => 'lights', 'misc_accessory' => 'miscAccessories'];

        if ($all) {
            foreach ($relations as $relation) {
                $firearm->{$relation}()->get()->each->update(['firearm_id' => null]);
            }

            return;
        }

        collect($selectedAccessories)
            ->unique(fn (array $accessory): string => $accessory['type'].':'.$accessory['id'])
            ->each(function (array $accessory) use ($firearm, $relations): void {
                $relation = $relations[$accessory['type']];
                $firearm->{$relation}()->whereKey($accessory['id'])->first()?->update(['firearm_id' => null]);
            });
    }
}
