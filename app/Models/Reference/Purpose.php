<?php

namespace App\Models\Reference;

use App\Models\Ammunition;
use App\Scopes\UserScope;
use Carbon\Carbon;
use Database\Factories\PurposeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $label
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Ammunition> $bullets
 */
class Purpose extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reference.purposes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['label', 'user_id'];

    /**
     * @return Factory<self>
     */
    protected static function newFactory(): Factory
    {
        return PurposeFactory::new();
    }

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
     * @param  Ammunition|null  $cartridge
     */
    public function totalRounds($cartridge = null): int
    {
        if ($cartridge) {
            return $this->bullets()
                ->where('cartridge_id', $cartridge->id)
                ->sum('inventory');
        }

        return $this->bullets()->sum('inventory');
    }

    /**
     * Whether the purpose is referenced by any ammunition load,
     * in which case it may not be deleted.
     */
    public function isInUse(): bool
    {
        return $this->bullets()->exists();
    }
}
