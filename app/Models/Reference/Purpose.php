<?php

namespace App\Models\Reference;

use App\Models\Ammunition;
use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Purpose extends Model implements Transformable
{
    use TransformableTrait;

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
        return $this->hasMany(Ammunition::class);
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
