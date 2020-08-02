<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class PrimerType extends Model implements Transformable
{
    use TransformableTrait;

    const BERDAN = 1;
    const BOXER = 2;
    const RIMFIRE = 3;

    public $timestamps = false;
}
