<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function totalRounds() {
        return $this->bullets()->sum('inventory');
    }

    public function firearms() {
        return $this->hasMany('App\Firearm');
    }
}
