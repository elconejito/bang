<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $dates = ['trip_date', 'created_at', 'updated_at', 'deleted_at'];

    public function range() {
        return $this->belongsTo('App\Range');
    }
    
    public function shoots() {
        return $this->hasMany('App\Shoot');
    }

    public function summary() {
        $firearms = [];
        foreach ( $this->shoots()->get() as $shoot ) {
            if ( isset($firearms[$shoot->firearm->label]) ) {
                $firearms[$shoot->firearm->label] = $firearms[$shoot->firearm->label] + $shoot->rounds;
            } else {
                $firearms[$shoot->firearm->label] = $shoot->rounds;
            }
        }

        return collect($firearms);
    }
}
