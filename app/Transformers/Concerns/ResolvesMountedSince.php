<?php

namespace App\Transformers\Concerns;

use App\Models\AccessoryEvent;
use Illuminate\Database\Eloquent\Model;

trait ResolvesMountedSince
{
    /**
     * Date of the most recent MOUNT event onto the accessory's current firearm.
     */
    protected function mountedSince(Model $accessory): ?string
    {
        if (! $accessory->firearm_id) {
            return null;
        }

        return AccessoryEvent::where('accessoryable_type', $accessory->getMorphClass())
            ->where('accessoryable_id', $accessory->id)
            ->where('event_type', 'MOUNT')
            ->where('firearm_id', $accessory->firearm_id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->value('event_date')?->toDateString();
    }
}
