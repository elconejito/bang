<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function order() {
        return $this->belongsTo('App\Order');
    }

    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }

    public function pictures() {
        return $this->hasMany('App\Picture');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getCostPerRound() {
        $cost = $this->cost_per_box / $this->rounds_per_box;

        return $cost;
    }
}
