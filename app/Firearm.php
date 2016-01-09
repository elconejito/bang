<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Firearm extends Model
{
    public function cartridge() {
        return $this->belongsTo('App\Cartridge');
    }
}
