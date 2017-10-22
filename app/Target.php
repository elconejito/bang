<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'label',
        'distance',
        'group_size',
        'picture_id',
        'trip_id',
        'shoot_id',
        'firearm_id',
        'bullet_id',
        'user_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }


    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function picture() {
        return $this->belongsTo(Picture::class);
    }

    public function trip() {
        return $this->belongsTo(Trip::class);
    }

    public function shoot() {
        return $this->belongsTo(Shoot::class);
    }

    public function firearm() {
        return $this->belongsTo(Firearm::class);
    }

    public function bullet() {
        return $this->belongsTo(Bullet::class);
    }
}
