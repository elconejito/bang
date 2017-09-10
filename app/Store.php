<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public function orders() {
        return $this->hasMany('App\Order');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
