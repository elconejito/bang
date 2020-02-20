<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Bullet extends Model
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
     * Each bullet belongs to a Cartridge type
     *
     * @return BelongsTo
     */
    public function cartridge() {
        return $this->belongsTo(Cartridge::class);
    }

    public function purpose() {
        return $this->belongsTo(Purpose::class);
    }

    public function pictures() {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    /**
     * @param Builder $query
     * @param Cartridge $cartridge
     *
     * @return $this
     */
    public function scopeForCartridge(Builder $query, Cartridge $cartridge)
    {
        return $query->where('cartridge_id', '=', $cartridge->id);
    }

    /**
     * @param Builder $query
     * @param Purpose $purpose
     *
     * @return $this
     */
    public function scopeForPurpose(Builder $query, Purpose $purpose)
    {
        return $query->where('purpose_id', '=', $purpose->id);
    }

    public function shoots() {
        return $this->hasMany(TrainingSession::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function inventory() {
        $added = $this->inventories()->sum('rounds');
        $fired = $this->shoots()->sum('rounds');

        $this->inventory = $added - $fired;

        $this->save();
    }

    public function getLabel($short = '') {
        $label = $this->manufacturer . " " . $this->name;
        if ( $short !== 'short' ) {
            $label .= ", " . $this->weight . "gr " . $this->cartridge->label;
        }
        return $label;
    }
}
