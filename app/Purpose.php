<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Purpose extends Model
{
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
