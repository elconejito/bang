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
        if (! $magazine->wasChanged('status')) {
            return;
        }

        $old = $magazine->getOriginal('status');
        $new = $magazine->status;

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
}
