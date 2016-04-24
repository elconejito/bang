<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bullet extends Model
{
    /**
     * Each bullet belongs to a Cartridge type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartridge() {
        return $this->belongsTo('App\Cartridge');
    }

    public function purpose() {
        return $this->belongsTo('App\Purpose');
    }

    public static function updateInventory() {
        foreach ( Bullet::all() as $bullet ) {
            $rounds_purchased = DB::table('inventories')
                ->where('bullet_id', $bullet->id)
                ->sum('rounds');

            $rounds_fired = DB::table('shoots')
                ->where('bullet_id', $bullet->id)
                ->sum('rounds');

            $bullet->inventory = $rounds_purchased - $rounds_fired;

            $bullet->save();
        }
    }
    
    public function getLabel() {
        return $this->manufacturer . " " . $this->model . ", " . $this->weight . "gr " . $this->cartridge->label;
    }
}
