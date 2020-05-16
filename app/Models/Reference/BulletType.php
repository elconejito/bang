<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class BulletType extends Model
{
    const RIMFIRE = 1;
    const CENTERFIRE = 2;
    const SHOTGUN = 3;
}
