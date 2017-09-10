<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    public function bullets() {
        return $this->belongsTo('App\Bullet');
    }

    public function firearms() {
        return $this->belongsTo('App\Firearm');
    }

    public function inventories() {
        return $this->belongsTo('App\Inventory');
    }

    public function shoots() {
        return $this->belongsTo('App\Shoot');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
