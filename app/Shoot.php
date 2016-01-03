<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shoot extends Model
{
    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }
}
