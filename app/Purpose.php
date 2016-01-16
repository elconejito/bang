<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purpose extends Model
{
    public function bullets() {
        return $this->hasMany('App\Bullet');
    }

    public function totalRounds($cartridge = null) {
        if ( $cartridge ) {
            return $this->bullets()
                ->where('cartridge_id', $cartridge->id)
                ->sum('inventory');
        }
        return $this->bullets()->sum('inventory');
    }
}
