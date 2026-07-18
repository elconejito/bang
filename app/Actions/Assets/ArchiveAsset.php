<?php

namespace App\Actions\Assets;

use App\Enums\ArchiveReason;
use App\Models\Accessory;
use App\Models\Magazine;
use Illuminate\Support\Facades\DB;

class ArchiveAsset
{
    public function execute(Accessory|Magazine $asset, ArchiveReason $reason, ?string $description, int $actorId): Accessory|Magazine
    {
        return DB::transaction(function () use ($asset, $reason, $description, $actorId): Accessory|Magazine {
            /** @var Accessory|Magazine $lockedAsset */
            $lockedAsset = $asset->newQuery()->whereKey($asset)->lockForUpdate()->firstOrFail();

            if ($lockedAsset->isArchived()) {
                return $lockedAsset;
            }

            $assignment = $lockedAsset instanceof Magazine ? 'current_firearm_id' : 'firearm_id';
            $lockedAsset->forceFill([
                $assignment => null,
                'archived_at' => now(),
                'archive_reason' => $reason,
                'archive_description' => $description,
            ])->save();

            $lockedAsset->activityEvents()->create([
                'user_id' => $lockedAsset->user_id,
                'actor_id' => $actorId,
                'type' => 'ARCHIVED',
                'occurred_at' => now(),
                'description' => $description,
                'metadata' => ['reason' => $reason->value],
            ]);

            return $lockedAsset->refresh();
        }, attempts: 3);
    }
}
