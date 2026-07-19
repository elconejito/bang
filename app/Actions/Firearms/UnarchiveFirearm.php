<?php

namespace App\Actions\Firearms;

use App\Models\Firearm;
use Illuminate\Support\Facades\DB;

class UnarchiveFirearm
{
    public function execute(Firearm $firearm, int $actorId): Firearm
    {
        return DB::transaction(function () use ($firearm, $actorId): Firearm {
            $lockedFirearm = Firearm::query()->whereKey($firearm)->lockForUpdate()->firstOrFail();

            if (! $lockedFirearm->isArchived()) {
                return $lockedFirearm;
            }

            $previousReason = $lockedFirearm->archive_reason?->value;
            $previousDescription = $lockedFirearm->archive_description;
            $lockedFirearm->update(['archived_at' => null, 'archive_reason' => null, 'archive_description' => null]);
            $lockedFirearm->activityEvents()->create([
                'user_id' => $lockedFirearm->user_id,
                'actor_id' => $actorId,
                'type' => 'UNARCHIVED',
                'occurred_at' => now(),
                'description' => $previousDescription,
                'metadata' => ['previous_reason' => $previousReason],
            ]);

            return $lockedFirearm->refresh();
        }, attempts: 3);
    }
}
