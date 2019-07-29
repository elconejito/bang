<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cartridge extends Model
{
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


    /**
     * A cartridge has many types of Bullets
     *
     * @return HasMany
     */
    public function bullets() {
        return $this->hasMany(Bullet::class);
    }

    public function bulletsForPurpose(Purpose $purpose)
    {
        return $this->bullets()->forPurpose($purpose)->get();
    }

    public function firearms() {
        return $this->hasMany(Firearm::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function scopePurposes() {
        foreach ( Purpose::all() as $purpose ) {

        }
        $inventory = Bullet::where('cartridge_id', $this->id)
            ->select(DB::raw('SUM(`inventory`) as inventory, purpose_id'))
            ->groupBy('purpose_id')
            ->get();
    }

    public function totalRounds() {
        return $this->bullets()->sum('inventory');
    }
}
