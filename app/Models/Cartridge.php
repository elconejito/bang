<?php

namespace App\Models;

use App\Models\Reference\Purpose;
use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property string $caliber
 * @property string $label
 * @property int $cartridge_type_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Ammunition> $bullets
 * @property-read Collection<int, Firearm> $firearms
 * @property-read Collection<int, Note> $notes
 * @property-read CaliberType|null $cartridgeType
 */
class Cartridge extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.cartridges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'caliber',
        'label',
        'cartridge_type_id',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return HasMany<Ammunition, self>
     */
    public function bullets(): HasMany
    {
        return $this->hasMany(Ammunition::class);
    }

    /**
     * @return Collection<int, Ammunition>
     */
    public function bulletsForPurpose(Purpose $purpose): Collection
    {
        return $this->bullets()->forPurpose($purpose)->get();
    }

    /**
     * @return BelongsTo<CaliberType, self>
     */
    public function cartridgeType(): BelongsTo
    {
        return $this->belongsTo(CaliberType::class);
    }

    /**
     * @return HasMany<Firearm, self>
     */
    public function firearms(): HasMany
    {
        return $this->hasMany(Firearm::class);
    }

    /**
     * @return MorphMany<Note, self>
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function totalRounds(): int
    {
        return $this->bullets()->sum('inventory');
    }
}
