<?php

namespace App\Observers;

use App\Models\AccessoryEvent;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AccessoryObserver
{
    public function created(Model $model): void
    {
        AccessoryEvent::create([
            'user_id' => Auth::id(),
            'accessoryable_type' => get_class($model),
            'accessoryable_id' => $model->id,
            'event_type' => 'ADDED',
            'event_date' => Carbon::today(),
        ]);
    }

    public function updated(Model $model): void
    {
        if ($model->wasChanged('firearm_id')) {
            $oldFirearmId = $model->getOriginal('firearm_id');
            $newFirearmId = $model->firearm_id;

            if ($oldFirearmId === null && $newFirearmId !== null) {
                AccessoryEvent::create([
                    'user_id' => Auth::id(),
                    'accessoryable_type' => get_class($model),
                    'accessoryable_id' => $model->id,
                    'event_type' => 'MOUNT',
                    'event_date' => Carbon::today(),
                    'firearm_id' => $newFirearmId,
                ]);
            } elseif ($oldFirearmId !== null && $newFirearmId === null) {
                AccessoryEvent::create([
                    'user_id' => Auth::id(),
                    'accessoryable_type' => get_class($model),
                    'accessoryable_id' => $model->id,
                    'event_type' => 'UNMOUNT',
                    'event_date' => Carbon::today(),
                    'firearm_id' => $oldFirearmId,
                ]);
            }
        }

        if ($model->wasChanged('location_id')) {
            $location = $model->location_id ? Location::find($model->location_id) : null;
            AccessoryEvent::create([
                'user_id' => Auth::id(),
                'accessoryable_type' => get_class($model),
                'accessoryable_id' => $model->id,
                'event_type' => 'LOCATION_CHANGE',
                'event_date' => Carbon::today(),
                'description' => $location ? "Moved to {$location->label}" : 'Removed from location',
            ]);
        }
    }
}
