<?php

namespace App\Observers;

use App\Models\AccessoryEvent;
use App\Models\Magazine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MagazineObserver
{
    public function created(Magazine $magazine): void
    {
        AccessoryEvent::create([
            'user_id' => Auth::id() ?? $magazine->user_id,
            'accessoryable_type' => Magazine::class,
            'accessoryable_id' => $magazine->id,
            'event_type' => 'ADDED',
            'event_date' => Carbon::today(),
        ]);
    }

    public function updated(Magazine $magazine): void
    {
        if (! $magazine->wasChanged(['current_firearm_id', 'loaded_rounds'])) {
            return;
        }

        $old = $this->displayStatus(
            $magazine->getRawOriginal('current_firearm_id'),
            (int) $magazine->getRawOriginal('loaded_rounds'),
        );
        $new = $magazine->display_status;

        if ($old === $new) {
            return;
        }

        $type = match (true) {
            $new === 'in_gun' => 'MOUNT',
            $old === 'in_gun' => 'UNMOUNT',
            $new === 'loaded' => 'LOAD',
            $old === 'loaded' && $new === 'empty' => 'UNLOAD',
            default => null,
        };

        if ($type === null) {
            return;
        }

        AccessoryEvent::create([
            'user_id' => Auth::id() ?? $magazine->user_id,
            'accessoryable_type' => Magazine::class,
            'accessoryable_id' => $magazine->id,
            'event_type' => $type,
            'event_date' => Carbon::today(),
        ]);
    }

    private function displayStatus(mixed $currentFirearmId, int $loadedRounds): string
    {
        if ($currentFirearmId !== null) {
            return 'in_gun';
        }

        return $loadedRounds > 0 ? 'loaded' : 'empty';
    }
}
