<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Caliber extends Model implements Transformable
{
    use BelongsToUser, HasNotes, TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

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
        return $this->belongsToMany(Firearm::class);
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
