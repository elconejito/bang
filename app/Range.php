<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Range extends Model
{
    public function shoots() {
        return $this->hasMany('App\Shoot');
    }
}
