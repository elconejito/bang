<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class CaliberType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.caliber_types';

    const CENTERFIRE = 1;
    const RIMFIRE = 2;
    const SHOTGUN = 3;
}
