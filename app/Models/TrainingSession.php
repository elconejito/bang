<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
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

    public function ammunition() {
        return $this->belongsTo(Ammunition::class);
    }

    public function firearm() {
        return $this->belongsTo(Firearm::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

}
