<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property string $abbreviation
 * @property int $user_id
 */
class BulletType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.bullet_types';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
