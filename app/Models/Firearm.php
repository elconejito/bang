<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Firearm extends Model
{
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


    public function cartridge() {
        return $this->belongsTo(Cartridge::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function totalRoundsFired() {
        return TrainingSession::where('firearm_id', $this->id)
                              ->sum('rounds');
    }
}
