<?php

namespace App\Actions\Firearms;

use App\Models\ActivityEvent;
use App\Models\Firearm;

class LogFirearmActivityEvent
{
    /**
     * @param  array{event_type: string, event_date: string, description?: string|null}  $attributes
     */
    public function execute(Firearm $firearm, int $actorId, array $attributes): ActivityEvent
    {
        return $firearm->activityEvents()->create([
            'user_id' => $actorId,
            'actor_id' => $actorId,
            'type' => $attributes['event_type'],
            'occurred_at' => $attributes['event_date'],
            'description' => $attributes['description'] ?? null,
        ]);
    }
}
