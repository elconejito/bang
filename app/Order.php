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

    /**
     * Relationships
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store() {
        return $this->belongsTo('App\Store');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories() {
        return $this->hasMany('App\Inventory');
    }

    public function getRounds() {
        if ( $this->rounds != 0 ) {
            $rounds = $this->rounds;
        } else {
            $rounds = $this->inventories()->sum('rounds');
        }
        return $rounds;
    }

    public function getTotalCost() {
        if ( $this->total_cost != 0.00 ) {
            $cost = $this->total_cost;
        } else {
            $cost = $this->inventories()->sum('cost');
        }
        return "$" . number_format($cost, 2);
    }

    public function updateCost() {
        $this->total_cost = $this->inventories()->sum('cost');
    }

    public function updateRounds() {
        $this->rounds = $this->inventories()->sum('rounds');
    }
}
