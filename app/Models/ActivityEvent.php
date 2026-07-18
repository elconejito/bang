<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $type
 * @property Carbon $occurred_at
 * @property int|null $actor_id
 * @property int|null $firearm_id
 * @property int|null $rounds
 * @property string|null $description
 * @property array<string, mixed>|null $metadata
 */
class ActivityEvent extends Model
{
    use BelongsToUser, HasFactory;

    protected $table = 'cms.accessory_events';

    protected $fillable = [
        'user_id', 'subject_type', 'subject_id', 'type', 'occurred_at',
        'actor_id', 'firearm_id', 'rounds', 'description', 'metadata',
        'accessoryable_type', 'accessoryable_id', 'event_type', 'event_date',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'event_date' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $event): void {
            $event->subject_type ??= $event->accessoryable_type;
            $event->subject_id ??= $event->accessoryable_id;
            $event->type ??= $event->event_type ? strtoupper($event->event_type) : null;
            $event->occurred_at ??= $event->event_date;
            $event->actor_id ??= $event->user_id;
            $event->accessoryable_type ??= $event->subject_type;
            $event->accessoryable_id ??= $event->subject_id;
            $event->event_type ??= $event->type;
            $event->event_date ??= $event->occurred_at;
        });
    }

    /** @return MorphTo<Model, self> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return MorphTo<Model, self> */
    public function accessoryable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }

    /** @return BelongsTo<Firearm, self> */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }
}
