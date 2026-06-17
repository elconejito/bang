<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string|null $label
 * @property string $manufacturer
 * @property string|null $model
 * @property string|null $serial
 * @property int|null $location_id
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
 * @property-read Store|null $purchaseStore
 */
class Firearm extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

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
        'serial',
        'location_id',
        'purchase_date',
        'purchase_price',
        'purchase_store_id',
        'user_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
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
        return $this->morphToMany(Picture::class, 'pictureable', 'cms.pictureables');
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

    /**
     * @return BelongsTo<Store, self>
     */
    public function purchaseStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'purchase_store_id');
    }

    /**
     * Sum of rounds across all training-session inventory deductions for this firearm.
     */
    public function totalRoundsFired(): int
    {
        return (int) Inventory::where('firearm_id', $this->id)
            ->whereNotNull('training_session_id')
            ->sum('rounds');
    }
}
