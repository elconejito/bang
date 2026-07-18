<?php

namespace App\Actions\Assets;

use App\Models\Accessory;
use App\Models\ActivityEvent;
use App\Models\Magazine;
use App\Models\SessionLine;
use App\Models\Suppressor;
use Illuminate\Support\Facades\DB;

class DeleteAsset
{
    /**
     * @return array<int, array{type: string, count: int, message: string}>
     */
    public function execute(Accessory|Magazine $asset): array
    {
        return DB::transaction(function () use ($asset): array {
            /** @var Accessory|Magazine $lockedAsset */
            $lockedAsset = $asset->newQuery()->whereKey($asset)->lockForUpdate()->firstOrFail();
            $blockers = $this->blockers($lockedAsset);

            if ($blockers !== []) {
                return $blockers;
            }

            $lockedAsset->pictures()->detach();

            if ($lockedAsset instanceof Magazine) {
                $lockedAsset->calibers()->detach();
                $lockedAsset->compatibleFirearms()->detach();
            }

            $lockedAsset->activityEvents()->delete();
            $lockedAsset->delete();

            return [];
        }, attempts: 3);
    }

    /** @return array<int, array{type: string, count: int, message: string}> */
    private function blockers(Accessory|Magazine $asset): array
    {
        if (! $asset->isArchived()) {
            return [$this->blocker('active', 1, 'Archive this item before permanently deleting it.')];
        }

        $counts = [
            'training_history' => $asset instanceof Suppressor
                ? SessionLine::query()->where('suppressor_id', $asset->id)->count()
                : 0,
            'operational_activity' => ActivityEvent::query()
                ->where('subject_type', $asset->getMorphClass())
                ->where('subject_id', $asset->id)
                ->whereNotIn('type', ['ADDED', 'ARCHIVED', 'UNARCHIVED'])
                ->count(),
        ];

        $messages = [
            'training_history' => 'This suppressor has training history and cannot be permanently deleted.',
            'operational_activity' => 'This item has operational activity and cannot be permanently deleted.',
        ];

        return collect($counts)
            ->filter()
            ->map(fn (int $count, string $type): array => $this->blocker($type, $count, $messages[$type]))
            ->values()
            ->all();
    }

    /** @return array{type: string, count: int, message: string} */
    private function blocker(string $type, int $count, string $message): array
    {
        return compact('type', 'count', 'message');
    }
}
