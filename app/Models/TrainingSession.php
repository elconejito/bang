<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property Carbon $session_date
 * @property int|null $location_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Ammunition|null $ammunition
 * @property-read Firearm|null $firearm
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, Target> $targets
 * @property-read Collection<int, Inventory> $inventories
 */
class TrainingSession extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.training_sessions';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'session_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'description',
        'session_date',
        'location_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }

    /**
     * @return BelongsTo<Firearm, self>
     */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }

    /**
     * @return HasMany<Picture, self>
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    /**
     * @return HasMany<Target, self>
     */
    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    /**
     * @return HasMany<Inventory, self>
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
