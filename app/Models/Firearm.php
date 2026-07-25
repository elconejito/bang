<?php

namespace App\Models;

use App\Enums\ArchiveReason;
use App\Enums\FirearmType;
use App\Models\Reference\Color;
use App\Traits\Archivable;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string|null $label
 * @property string $manufacturer
 * @property string|null $model
 * @property string|null $customizer
 * @property string|null $custom_package
 * @property string|null $serial
 * @property FirearmType|null $type
 * @property int|null $location_id
 * @property int|null $color_id
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property int|null $purchase_store_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Caliber> $calibers
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, Target> $targets
 * @property-read Location|null $location
 * @property-read Color|null $color
 * @property-read Store|null $purchaseStore
 * @property-read Collection<int, Suppressor> $suppressors
 * @property-read Collection<int, Optic> $optics
 * @property-read Collection<int, Light> $lights
 * @property-read Collection<int, MiscAccessory> $miscAccessories
 * @property-read Collection<int, Mount> $mounts
 * @property-read Collection<int, Magazine> $magazines
 * @property-read Collection<int, Magazine> $currentMagazines
 */
class Firearm extends Model
{
    use Archivable, BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.firearms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'manufacturer',
        'model',
        'customizer',
        'custom_package',
        'serial',
        'type',
        'location_id',
        'color_id',
        'purchase_date',
        'purchase_price',
        'purchase_store_id',
        'user_id',
        'archived_at',
        'archive_reason',
        'archive_description',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'type' => FirearmType::class,
        'archived_at' => 'datetime',
        'archive_reason' => ArchiveReason::class,
    ];

    /**
     * @return BelongsToMany<Caliber, self>
     */
    public function calibers(): BelongsToMany
    {
        return $this->belongsToMany(Caliber::class, 'cms.caliber_firearm');
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

    /**
     * @return HasMany<Target, self>
     */
    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    /**
     * @return BelongsTo<Location, self>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<Color, self> */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * @return BelongsTo<Store, self>
     */
    public function purchaseStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'purchase_store_id');
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

    /** @return HasMany<Mount, self> */
    public function mounts(): HasMany
    {
        return $this->hasMany(Mount::class);
    }

    /**
     * @return BelongsToMany<Magazine, self>
     */
    public function magazines(): BelongsToMany
    {
        return $this->belongsToMany(Magazine::class, 'cms.firearm_magazine');
    }

    /** @return HasMany<Magazine, self> */
    public function currentMagazines(): HasMany
    {
        return $this->hasMany(Magazine::class, 'current_firearm_id');
    }

    /** @return MorphMany<ActivityEvent, self> */
    public function activityEvents(): MorphMany
    {
        return $this->morphMany(ActivityEvent::class, 'subject');
    }

    /**
     * Sum of rounds across session lines with add_firearm_count enabled.
     */
    public function totalRoundsFired(): int
    {
        return (int) SessionLine::where('firearm_id', $this->id)
            ->where('add_firearm_count', true)
            ->sum('rounds');
    }
}
