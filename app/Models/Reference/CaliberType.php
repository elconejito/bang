<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class CaliberType extends Model
{
    use TransformableTrait;

    const CENTERFIRE = 1;
    const RIMFIRE = 2;
    const SHOTGUN = 3;
}
