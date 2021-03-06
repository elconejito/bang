<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Range extends Model
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


    public function shoots() {
        return $this->hasMany(TrainingSession::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
