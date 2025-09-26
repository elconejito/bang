<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class PrimerType extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.primer_types';

    const BERDAN = 1;
    const BOXER = 2;
    const RIMFIRE = 3;

    public $timestamps = false;
}
