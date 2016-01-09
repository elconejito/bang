<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purpose extends Model
{
    public function bullets() {
        return $this->hasMany('App\Bullet');
    }
}
