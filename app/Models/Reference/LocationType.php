<?php

namespace App\Models\Reference;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class LocationType extends Model 
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.location_types';

    public $timestamps = false;

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
