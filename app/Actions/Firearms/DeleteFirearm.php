<?php

namespace App\Actions\Firearms;

use App\Models\ActivityEvent;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\SessionLine;
use App\Models\Target;
use Illuminate\Support\Facades\DB;

class DeleteFirearm
{
    /**
     * @return array<int, array{type: string, count: int, message: string}>
     */
    public function execute(Firearm $firearm): array
    {
        return DB::transaction(function () use ($firearm): array {
            $lockedFirearm = Firearm::query()->whereKey($firearm)->lockForUpdate()->firstOrFail();
            $blockers = $this->blockers($lockedFirearm);

            if ($blockers !== []) {
                return $blockers;
            }

            $lockedFirearm->calibers()->detach();
            $lockedFirearm->magazines()->detach();
            $lockedFirearm->pictures()->detach();
            $lockedFirearm->activityEvents()->delete();
            $lockedFirearm->delete();

            return [];
        }, attempts: 3);
    }

    /** @return array<int, array{type: string, count: int, message: string}> */
    private function blockers(Firearm $firearm): array
    {
        if (! $firearm->isArchived()) {
            return [$this->blocker('active', 1, 'Archive this firearm before permanently deleting it.')];
        }

        $counts = [
            'mounted_accessories' => $firearm->suppressors()->count() + $firearm->optics()->count() + $firearm->lights()->count() + $firearm->miscAccessories()->count(),
            'inserted_magazines' => $firearm->currentMagazines()->count(),
            'training_history' => SessionLine::query()->where('firearm_id', $firearm->id)->count(),
            'targets' => Target::query()->where('firearm_id', $firearm->id)->count(),
            'inventory_history' => Inventory::query()->where('firearm_id', $firearm->id)->count(),
            'operational_activity' => ActivityEvent::query()
                ->where(function ($query) use ($firearm): void {
                    $query->where(function ($subjectQuery) use ($firearm): void {
                        $subjectQuery->where('subject_type', $firearm->getMorphClass())
                            ->where('subject_id', $firearm->id);
                    })->orWhere('firearm_id', $firearm->id);
                })
                ->whereNotIn('type', ['ADDED', 'ARCHIVED', 'UNARCHIVED'])
                ->count(),
        ];

        $messages = [
            'mounted_accessories' => 'Unmount all accessories before deleting this firearm.',
            'inserted_magazines' => 'Remove the inserted magazine before deleting this firearm.',
            'training_history' => 'This firearm has training history and cannot be permanently deleted.',
            'targets' => 'This firearm has target history and cannot be permanently deleted.',
            'inventory_history' => 'This firearm has inventory history and cannot be permanently deleted.',
            'operational_activity' => 'This firearm has operational activity and cannot be permanently deleted.',
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
