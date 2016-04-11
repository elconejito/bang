<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $dates = ['trip_date', 'created_at', 'updated_at', 'deleted_at'];

    public function range() {
        return $this->belongsTo('App\Range');
    }
}
