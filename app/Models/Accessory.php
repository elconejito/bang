<?php

namespace App\Models;

use App\Enums\ArchiveReason;
use App\Models\Reference\Color;
use App\Traits\Archivable;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $manufacturer
 * @property string $label
 * @property string|null $serial
 * @property int|null $firearm_id
 * @property int|null $location_id
 * @property int|null $color_id
 * @property Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property int|null $purchase_store_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Firearm|null $firearm
 * @property-read Location|null $location
 * @property-read Color|null $color
 * @property-read Store|null $purchaseStore
 * @property-read Collection<int, AccessoryEvent> $events
 * @property-read Collection<int, Picture> $pictures
 */
abstract class Accessory extends Model
{
    use Archivable, BelongsToUser, HasFactory, HasNotes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_price' => 'float',
            'archived_at' => 'datetime',
            'archive_reason' => ArchiveReason::class,
        ];
    }

    /**
     * @return BelongsTo<Firearm, self>
     */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
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
     * @return MorphMany<AccessoryEvent, self>
     */
    public function events(): MorphMany
    {
        return $this->morphMany(AccessoryEvent::class, 'accessoryable');
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
