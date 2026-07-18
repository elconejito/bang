<?php

namespace App\Actions\Assets;

use App\Models\Accessory;
use App\Models\Magazine;
use Illuminate\Support\Facades\DB;

class UnarchiveAsset
{
    public function execute(Accessory|Magazine $asset, int $actorId): Accessory|Magazine
    {
        return DB::transaction(function () use ($asset, $actorId): Accessory|Magazine {
            /** @var Accessory|Magazine $lockedAsset */
            $lockedAsset = $asset->newQuery()->whereKey($asset)->lockForUpdate()->firstOrFail();

            if (! $lockedAsset->isArchived()) {
                return $lockedAsset;
            }

            $previousReason = $lockedAsset->archive_reason?->value;
            $previousDescription = $lockedAsset->archive_description;
            $lockedAsset->forceFill(['archived_at' => null, 'archive_reason' => null, 'archive_description' => null])->save();
            $lockedAsset->activityEvents()->create([
                'user_id' => $lockedAsset->user_id,
                'actor_id' => $actorId,
                'type' => 'UNARCHIVED',
                'occurred_at' => now(),
                'description' => $previousDescription,
                'metadata' => ['previous_reason' => $previousReason],
            ]);

            return $lockedAsset->refresh();
        }, attempts: 3);
    }
}
