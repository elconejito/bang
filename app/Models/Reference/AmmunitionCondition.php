<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class AmmunitionCondition extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = false;
}