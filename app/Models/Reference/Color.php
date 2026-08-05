<?php

namespace App\Models\Reference;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Scopes\UserScope;
use Database\Factories\ColorFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reference.colors';

    protected $fillable = ['label', 'short_label', 'user_id'];

    /** @return Factory<self> */
    protected static function newFactory(): Factory
    {
        return ColorFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /** @return HasMany<Firearm, self> */
    public function firearms(): HasMany
    {
        return $this->hasMany(Firearm::class);
    }

    /** @return HasMany<Suppressor, self> */
    public function suppressors(): HasMany
    {
        return $this->hasMany(Suppressor::class);
    }

    /** @return HasMany<Optic, self> */
    public function optics(): HasMany
    {
        return $this->hasMany(Optic::class);
    }

    /** @return HasMany<Light, self> */
    public function lights(): HasMany
    {
        return $this->hasMany(Light::class);
    }

    /** @return HasMany<MiscAccessory, self> */
    public function miscAccessories(): HasMany
    {
        return $this->hasMany(MiscAccessory::class);
    }

    /** @return HasMany<Magazine, self> */
    public function magazines(): HasMany
    {
        return $this->hasMany(Magazine::class);
    }

    public function isInUse(): bool
    {
        return $this->firearms()->exists()
            || $this->suppressors()->exists()
            || $this->optics()->exists()
            || $this->lights()->exists()
            || $this->miscAccessories()->exists()
            || $this->magazines()->exists();
    }
}
