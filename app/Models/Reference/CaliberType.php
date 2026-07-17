<?php

namespace App\Models\Reference;

use Carbon\Carbon;
use Database\Factories\CaliberTypeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CaliberType extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.caliber_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label'];

    /**
     * @return Factory<self>
     */
    protected static function newFactory(): Factory
    {
        return CaliberTypeFactory::new();
    }
}
