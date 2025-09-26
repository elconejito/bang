<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class AmmunitionCasing extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.ammunition_casings';

    public $timestamps = false;
}
