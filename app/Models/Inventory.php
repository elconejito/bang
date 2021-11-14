<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'inventory_date',
        'rounds',
        'ammunition_id',
        'order_id',
        'user_id',
    ];

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


    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function bullet() {
        return $this->belongsTo(Ammunition::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getCostPerRound() {
        $cost = $this->cost_per_box / $this->rounds_per_box;

        return $cost;
    }
}
