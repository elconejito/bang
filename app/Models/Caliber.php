<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caliber extends Model
{
    use BelongsToUser, HasNotes;

    /**
     * A cartridge has many types of Bullets
     *
     * @return HasMany
     */
    public function ammunition()
    {
        return $this->hasMany(Ammunition::class);
    }

    public function ammunitionForPurpose(Purpose $purpose)
    {
        return $this->ammunition()->forPurpose($purpose)->get();
    }

    public function caliberType()
    {
        return $this->belongsTo(CaliberType::class);
    }

    public function firearms()
    {
        return $this->hasMany(Firearm::class);
    }

    public function scopePurposes()
    {
        $inventory = Ammunition::where('caliber_id', $this->id)
                               ->select(DB::raw('SUM(`inventory`) as inventory, purpose_id'))
                               ->groupBy('purpose_id')
                               ->get();
    }

    public function totalRounds()
    {
        return $this->ammunition()->sum('inventory');
    }
}
