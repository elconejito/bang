<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Firearm extends Model
{
    public function cartridge() {
        return $this->belongsTo('App\Cartridge');
    }

    public function totalRoundsFired() {
        return Shoot::where('firearm_id', $this->id)
            ->sum('rounds');
    }
}
