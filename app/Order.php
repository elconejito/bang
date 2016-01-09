<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['order_date', 'created_at', 'updated_at', 'deleted_at'];
    
    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }

    public function store() {
        return $this->belongsTo('App\Store');
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
