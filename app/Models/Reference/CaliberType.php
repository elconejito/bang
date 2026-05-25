<?php

namespace App\Models\Reference;

use Database\Factories\CaliberTypeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaliberType extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'user_id'];

    protected static function newFactory(): Factory
    {
        return CaliberTypeFactory::new();
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.caliber_types';

    const CENTERFIRE = 1;

    const RIMFIRE = 2;

    const SHOTGUN = 3;
}
