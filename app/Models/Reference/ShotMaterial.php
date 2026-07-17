<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 */
class ShotMaterial extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.shot_materials';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
