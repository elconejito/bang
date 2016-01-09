<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shoot extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['shoot_date', 'created_at', 'updated_at', 'deleted_at'];
    
    public function bullet() {
        return $this->belongsTo('App\Bullet');
    }

    public function firearm() {
        return $this->belongsTo('App\Firearm');
    }
}
