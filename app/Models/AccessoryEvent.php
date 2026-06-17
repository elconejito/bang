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
 * @property string $accessoryable_type
 * @property int $accessoryable_id
 * @property string $event_type
 * @property Carbon $event_date
 * @property int|null $firearm_id
 * @property int|null $rounds
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Firearm|null $firearm
 */
class AccessoryEvent extends Model
{
    use BelongsToUser, HasFactory;

    protected $table = 'cms.accessory_events';

    protected $fillable = [
        'user_id',
        'accessoryable_type',
        'accessoryable_id',
        'event_type',
        'event_date',
        'firearm_id',
        'rounds',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    /**
     * @return MorphTo<Model, self>
     */
    public function accessoryable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Firearm, self>
     */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }
}
