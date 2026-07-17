<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 */
class PrimerType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.primer_types';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
