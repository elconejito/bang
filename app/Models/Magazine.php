<?php

namespace App\Models;

use App\Enums\ArchiveReason;
use App\Models\Reference\Color;
use App\Traits\Archivable;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string|null $label
 * @property string $manufacturer
 * @property string|null $model_name
 * @property int $capacity
 * @property string|null $serial_number
 * @property string|null $id_marking
 * @property int|null $loaded_ammunition_id
 * @property int|null $location_id
 * @property int|null $color_id
 * @property int|null $current_firearm_id
 * @property int $loaded_rounds
 * @property-read string $display_status
 * @property-read string $load_state
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, Caliber> $calibers
 * @property-read Collection<int, Firearm> $compatibleFirearms
 * @property-read Collection<int, Note> $notes
 * @property-read Ammunition|null $loadedAmmunition
 * @property-read Location|null $location
 * @property-read Color|null $color
 * @property-read Firearm|null $currentFirearm
 */
class Magazine extends Model
{
    use Archivable, BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.magazines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'manufacturer',
        'model_name',
        'capacity',
        'serial_number',
        'id_marking',
        'loaded_ammunition_id',
        'location_id',
        'color_id',
        'current_firearm_id',
        'loaded_rounds',
        'user_id',
        'archived_at',
        'archive_reason',
        'archive_description',
    ];

    /** @var array<string, mixed> */
    protected $attributes = ['loaded_rounds' => 0];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'loaded_rounds' => 'integer',
            'archived_at' => 'datetime',
            'archive_reason' => ArchiveReason::class,
        ];
    }

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function loadedAmmunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class, 'loaded_ammunition_id');
    }

    /** @return BelongsTo<Location, self> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<Color, self> */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /** @return BelongsTo<Firearm, self> */
    public function currentFirearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class, 'current_firearm_id');
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
     * @return BelongsToMany<Caliber, self>
     */
    public function calibers(): BelongsToMany
    {
        return $this->belongsToMany(Caliber::class, 'cms.caliber_magazine');
    }

    /**
     * @return BelongsToMany<Firearm, self>
     */
    public function compatibleFirearms(): BelongsToMany
    {
        return $this->belongsToMany(Firearm::class, 'cms.firearm_magazine');
    }

    /** @return BelongsToMany<Firearm, self> */
    public function firearms(): BelongsToMany
    {
        return $this->compatibleFirearms();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->current_firearm_id !== null) {
            return 'in_gun';
        }

        return $this->loaded_rounds > 0 ? 'loaded' : 'empty';
    }

    public function getLoadStateAttribute(): string
    {
        return $this->loaded_rounds > 0 ? 'loaded' : 'empty';
    }

    public function scopeCompatibleWithFirearm(Builder $query, Firearm|int $firearm): Builder
    {
        $firearmId = $firearm instanceof Firearm ? $firearm->getKey() : $firearm;

        return $query->whereHas('compatibleFirearms', fn (Builder $query): Builder => $query->whereKey($firearmId));
    }

    public function scopeStoredAt(Builder $query, Location|int $location): Builder
    {
        $locationId = $location instanceof Location ? $location->getKey() : $location;

        return $query->where('location_id', $locationId)->whereNull('current_firearm_id');
    }

    public function scopeInFirearm(Builder $query, Firearm|int|null $firearm = null): Builder
    {
        return $query->whereNotNull('current_firearm_id')
            ->when($firearm !== null, function (Builder $query) use ($firearm): void {
                $query->where('current_firearm_id', $firearm instanceof Firearm ? $firearm->getKey() : $firearm);
            });
    }

    public function scopeLoaded(Builder $query): Builder
    {
        return $query->where('loaded_rounds', '>', 0);
    }

    public function scopeEmpty(Builder $query): Builder
    {
        return $query->where('loaded_rounds', 0);
    }

    /**
     * @return MorphMany<AccessoryEvent, self>
     */
    public function events(): MorphMany
    {
        return $this->morphMany(AccessoryEvent::class, 'accessoryable');
    }
}
