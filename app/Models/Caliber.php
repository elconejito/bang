<?php

namespace App\Models;

use App\Models\Reference\CaliberType;
use App\Models\Reference\Purpose;
use App\Traits\BelongsToUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $caliber
 * @property string|null $label
 * @property int $caliber_type_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CaliberType $caliberType
 * @property-read Collection<int, Ammunition> $ammunition
 * @property-read Collection<int, Firearm> $firearms
 */
class Caliber extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.calibers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'caliber',
        'label',
        'caliber_type_id',
        'user_id',
    ];

    /**
     * @return HasMany<Ammunition, self>
     */
    public function ammunition(): HasMany
    {
        return $this->hasMany(Ammunition::class);
    }

    /**
     * @return Collection<int, Ammunition>
     */
    public function ammunitionForPurpose(Purpose $purpose): Collection
    {
        return $this->ammunition()->forPurpose($purpose)->get();
    }

    /**
     * @return BelongsTo<CaliberType, self>
     */
    public function caliberType(): BelongsTo
    {
        return $this->belongsTo(CaliberType::class);
    }

    /**
     * @return BelongsToMany<Firearm, self>
     */
    public function firearms(): BelongsToMany
    {
        return $this->belongsToMany(Firearm::class, 'cms.caliber_firearm');
    }

    public function totalRounds(): int
    {
        return $this->ammunition()->sum('inventory');
    }

    /**
     * Whether the caliber is referenced by any firearm or ammunition load,
     * in which case it may not be deleted.
     */
    public function isInUse(): bool
    {
        return $this->firearms()->exists() || $this->ammunition()->exists();
    }
}
