<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bullet extends Model
{
    public function cartridge() {
        return $this->belongsToMany('App\Cartridge');
    }
}
