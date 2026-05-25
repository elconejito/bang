<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property int $user_id
 */
class PrimerType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.primer_types';

    const BERDAN = 1;

    const BOXER = 2;

    const RIMFIRE = 3;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
