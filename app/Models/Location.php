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
 * @property int|null $parent_location_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read LocationType|null $type
 * @property-read Location|null $parent
 * @property-read Collection<int, Location> $children
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
        'parent_location_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<LocationType, self>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class);
    }

    /** @return BelongsTo<Location, self> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_location_id');
    }

    /** @return BelongsTo<Location, self> */
    public function parentRecursive(): BelongsTo
    {
        return $this->parent()->with('parentRecursive');
    }

    /** @return HasMany<Location, self> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_location_id');
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

    /** @return HasMany<Mount, self> */
    public function mounts(): HasMany
    {
        return $this->hasMany(Mount::class);
    }

    /** @return HasMany<TrainingSession, self> */
    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function getFullLabelAttribute(): string
    {
        $labels = [$this->label];
        $visitedLocationIds = [$this->id => true];
        $parent = $this->parentRecursive;

        while ($parent !== null && ! isset($visitedLocationIds[$parent->id])) {
            array_unshift($labels, $parent->label);
            $visitedLocationIds[$parent->id] = true;
            $parent = $parent->parentRecursive;
        }

        return implode(' › ', $labels);
    }

    public function wouldCreateCycleWith(Location $parent): bool
    {
        $visitedLocationIds = [];
        $current = $parent;

        while ($current !== null && ! isset($visitedLocationIds[$current->id])) {
            if ($current->is($this)) {
                return true;
            }

            $visitedLocationIds[$current->id] = true;
            $current = $current->parent;
        }

        return false;
    }

    public function isInUse(): bool
    {
        return $this->children()->exists()
            || $this->firearms()->exists()
            || $this->suppressors()->exists()
            || $this->optics()->exists()
            || $this->lights()->exists()
            || $this->miscAccessories()->exists()
            || $this->mounts()->exists()
            || $this->magazines()->exists()
            || $this->trainingSessions()->exists();
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
