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

    public function updateInventory() {
        $rounds = DB::table('orders')
            ->where('bullet_id', $this->id)
            ->sum('rounds');

        $this->inventory = $rounds;

        $this->save();
    }
}
