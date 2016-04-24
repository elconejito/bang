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

    public function updateInventory() {
        $rounds_purchased = DB::table('inventories')
            ->where('bullet_id', $this->id)
            ->sum('rounds');

        $rounds_fired = DB::table('shoots')
            ->where('bullet_id', $this->id)
            ->sum('rounds');

        $this->inventory = $rounds_purchased - $rounds_fired;

        $this->save();
    }
    
    public function getLabel() {
        return $this->manufacturer . " " . $this->model . ", " . $this->weight . "gr " . $this->cartridge->label;
    }
}
