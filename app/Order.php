<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }

    public function getCost() {
        $cost = $this->boxes * $this->cost_per_box;

        return $cost;
    }
    public function getCostPerRound() {
        $cost = $this->cost_per_box / $this->rounds_per_box;

        return $cost;
    }
}
