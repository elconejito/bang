<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Shoot extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

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


    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }

    public function firearm() {
        return $this->belongsTo('App\Firearm');
    }

    public function trip() {
        return $this->belongsTo('App\Trip');
    }

    public function pictures() {
        return $this->hasMany('App\Picture');
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
