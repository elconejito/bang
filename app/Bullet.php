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

    public function pictures() {
        return $this->hasMany('App\Picture');
    }

    public function inventories() {
        return $this->hasMany('App\Inventory');
    }

    public function shoots() {
        return $this->hasMany('App\Shoot');
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
    
    public function getLabel($short = '') {
        $label = $this->manufacturer . " " . $this->model;
        if ( $short !== 'short' ) {
            $label .= ", " . $this->weight . "gr " . $this->cartridge->label;
        }
        return $label;
    }
}
