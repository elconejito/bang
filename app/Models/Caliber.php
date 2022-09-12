<?php

namespace App\Models;

use App\Models\Reference\CaliberType;
use App\Models\Reference\Purpose;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Caliber extends Model implements Transformable
{
    use BelongsToUser, HasNotes, TransformableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.calibers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label',
        'short_label',
        'caliber_type_id',
        'user_id',
    ];

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
