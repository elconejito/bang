<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class BulletType extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.bullet_types';

    public $timestamps = false;
}
