<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class AmmunitionCondition extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.ammunition_conditions';

    public $timestamps = false;
}
