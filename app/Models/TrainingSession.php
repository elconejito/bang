<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 */
class TrainingSession extends Model
{
    use BelongsToUser, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.training_sessions';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'session_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'label',
        'description',
        'session_date',
        'location_id',
        'user_id',
    ];

    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }

    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
