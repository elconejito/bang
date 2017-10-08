<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
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


    public function orders() {
        return $this->hasMany('App\Order');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
