<?php

namespace App\Models;

use App\Models\Reference\LocationType;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property int|null $location_type_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read LocationType|null $type
 * @property-read Collection<int, Firearm> $firearms
 * @property-read Collection<int, Suppressor> $suppressors
 * @property-read Collection<int, Optic> $optics
 * @property-read Collection<int, Light> $lights
 * @property-read Collection<int, MiscAccessory> $miscAccessories
 * @property-read Collection<int, Magazine> $magazines
 * @property-read Collection<int, Picture> $pictures
 */
class Location extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'description',
        'location_type_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<LocationType, self>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class);
    }

    /**
     * @return HasMany<Firearm, self>
     */
    public function firearms(): HasMany
    {
        return $this->hasMany(Firearm::class);
    }

    /**
     * @return HasMany<Suppressor, self>
     */
    public function suppressors(): HasMany
    {
        return $this->hasMany(Suppressor::class);
    }

    /**
     * @return HasMany<Optic, self>
     */
    public function optics(): HasMany
    {
        return $this->hasMany(Optic::class);
    }

    /**
     * @return HasMany<Light, self>
     */
    public function lights(): HasMany
    {
        return $this->hasMany(Light::class);
    }

    /**
     * @return HasMany<MiscAccessory, self>
     */
    public function miscAccessories(): HasMany
    {
        return $this->hasMany(MiscAccessory::class);
    }

    /** @return HasMany<Magazine, self> */
    public function magazines(): HasMany
    {
        return $this->hasMany(Magazine::class);
    }

    /**
     * @return MorphToMany<Picture, self>
     */
    public function pictures(): MorphToMany
    {
        return $this->morphToMany(Picture::class, 'pictureable', 'cms.pictureables')
            ->withPivot('sort_order', 'is_primary')
            ->orderByPivot('sort_order');
    }
}
