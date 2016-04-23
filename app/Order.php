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
            echo "!0";
            $rounds = $this->rounds;
        } else {
            echo "0";
            $rounds = $this->inventories()->sum('rounds');
            $this->rounds = $rounds;
            $this->save();
        }
        return $rounds;
    }

    public function getTotalCost() {
        if ( $this->total_cost != 0.00 ) {
            echo "!0";
            $cost = $this->total_cost;
        } else {
            echo "0";
            $cost = $this->inventories()->sum('cost');
            $this->total_cost = $cost;
            $this->save();
        }
        return $cost;
    }
}
