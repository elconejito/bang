<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use BelongsToUser, HasNotes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function ammunition() {
        return $this->belongsTo(Ammunition::class);
    }

    public function firearm() {
        return $this->belongsTo(Firearm::class);
    }

    public function trip() {
        return $this->belongsTo(Training::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

}
