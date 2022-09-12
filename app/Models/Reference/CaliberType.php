<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class CaliberType extends Model
{
    use TransformableTrait;

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
