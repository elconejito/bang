<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
