<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class ShotMaterial extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.shot_materials';

    public $timestamps = false;
}
