<?php

namespace App\Models;

use App\Models\Reference\Purpose;
use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

class Cartridge extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.cartridges';

    protected $fillable = [
        'caliber',
        'label',
        'cartridge_type_id',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    public function bullets(): HasMany
    {
        return $this->hasMany(Ammunition::class);
    }

    public function bulletsForPurpose(Purpose $purpose): Collection
    {
        return $this->bullets()->forPurpose($purpose)->get();
    }

    public function cartridgeType(): BelongsTo
    {
        return $this->belongsTo(CaliberType::class);
    }

    public function firearms(): HasMany
    {
        return $this->hasMany(Firearm::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function totalRounds(): int
    {
        return $this->bullets()->sum('inventory');
    }
}
