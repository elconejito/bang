<?php

namespace App\Observers;

use App\Models\AccessoryEvent;
use App\Models\Firearm;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AccessoryObserver
{
    public function created(Model $model): void
    {
        AccessoryEvent::create([
            'user_id' => Auth::id() ?? $model->user_id,
            'accessoryable_type' => get_class($model),
            'accessoryable_id' => $model->id,
            'event_type' => 'ADDED',
            'event_date' => Carbon::today(),
        ]);

        if ($model->firearm_id !== null) {
            AccessoryEvent::create([
                'user_id' => Auth::id() ?? $model->user_id,
                'accessoryable_type' => get_class($model),
                'accessoryable_id' => $model->id,
                'event_type' => 'MOUNT',
                'event_date' => Carbon::today(),
                'firearm_id' => $model->firearm_id,
            ]);
        }
    }

    public function updated(Model $model): void
    {
        if ($model->wasChanged('firearm_id')) {
            $oldFirearmId = $model->getOriginal('firearm_id');
            $newFirearmId = $model->firearm_id;

            if ($oldFirearmId !== null) {
                AccessoryEvent::create([
                    'user_id' => Auth::id() ?? $model->user_id,
                    'accessoryable_type' => get_class($model),
                    'accessoryable_id' => $model->id,
                    'event_type' => 'UNMOUNT',
                    'event_date' => Carbon::today(),
                    'firearm_id' => $oldFirearmId,
                ]);
            }

            if ($newFirearmId !== null) {
                // Mounted onto a firearm — note where it came from on a direct move.
                // Resolve without the user global scope so it works outside a request.
                $fromFirearm = $oldFirearmId
                    ? Firearm::withoutGlobalScopes()->where('user_id', $model->user_id)->find($oldFirearmId)
                    : null;

                AccessoryEvent::create([
                    'user_id' => Auth::id() ?? $model->user_id,
                    'accessoryable_type' => get_class($model),
                    'accessoryable_id' => $model->id,
                    'event_type' => 'MOUNT',
                    'event_date' => Carbon::today(),
                    'firearm_id' => $newFirearmId,
                    'description' => $fromFirearm
                        ? 'Moved from '.($fromFirearm->label ?? $fromFirearm->manufacturer)
                        : null,
                ]);
            }
        }

        if ($model->wasChanged('location_id')) {
            $location = $model->location_id ? Location::find($model->location_id) : null;
            AccessoryEvent::create([
                'user_id' => Auth::id() ?? $model->user_id,
                'accessoryable_type' => get_class($model),
                'accessoryable_id' => $model->id,
                'event_type' => 'LOCATION_CHANGE',
                'event_date' => Carbon::today(),
                'description' => $location ? "Moved to {$location->label}" : 'Removed from location',
            ]);
        }
    }
}
