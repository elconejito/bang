<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cartridge extends Model
{
    /**
     * A cartridge has many types of Bullets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bullets() {
        return $this->hasMany('App\Bullet');
    }

    public function firearms() {
        return $this->hasMany('App\Firearm');
    }

    public function scopePurposes() {
        foreach ( Purpose::all() as $purpose ) {

        }
        $inventory = Bullet::where('cartridge_id', $this->id)
            ->select(DB::raw('SUM(`inventory`) as inventory, purpose_id'))
            ->groupBy('purpose_id')
            ->get();
    }

    public function totalRounds() {
        return $this->bullets()->sum('inventory');
    }
}
