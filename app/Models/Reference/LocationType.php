<?php

namespace App\Models\Reference;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property int $user_id
 * @property-read Collection<int, Location> $locations
 */
class LocationType extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.location_types';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany<Location, self>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
