<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
class ShellType extends Model 
{
    use;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.shell_types';

    public $timestamps = false;
}
