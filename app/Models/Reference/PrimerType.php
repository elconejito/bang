<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class PrimerType extends Model
{
    const BERDAN = 1;
    const BOXER = 2;
    const RIMFIRE = 3;

    public $timestamps = false;
}
