<?php

namespace App\Models\Reference;

use App\Models\Ammunition;
use App\Scopes\UserScope;
use Database\Factories\PurposeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purpose extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    use HasFactory;

    protected $table = 'reference.purposes';

    protected $fillable = ['label', 'user_id'];

    protected static function newFactory(): Factory
    {
        return PurposeFactory::new();
    }

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

    public function totalRounds($cartridge = null): int
    {
        if ($cartridge) {
            return $this->bullets()
                ->where('cartridge_id', $cartridge->id)
                ->sum('inventory');
        }

        return $this->bullets()->sum('inventory');
    }
}
