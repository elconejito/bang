<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 */
class ShotWeight extends Model
{
    protected $table = 'reference.shot_weights';

    public $timestamps = false;
}
