<?php

namespace App\Models;

use App\Models\Reference\CaliberType;
use App\Models\Reference\Purpose;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Caliber extends Model
{
    use BelongsToUser, HasNotes;

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
        'caliber',
        'label',
        'caliber_type_id',
        'user_id',
    ];

    /**
     * A cartridge has many types of Bullets
     */
    public function ammunition(): HasMany
    {
        return $this->hasMany(Ammunition::class);
    }

    public function ammunitionForPurpose(Purpose $purpose): Collection
    {
        return $this->ammunition()->forPurpose($purpose)->get();
    }

    public function caliberType(): BelongsTo
    {
        return $this->belongsTo(CaliberType::class);
    }

    public function firearms(): BelongsToMany
    {
        return $this->belongsToMany(Firearm::class, 'cms.caliber_firearm');
    }

    public function totalRounds(): int
    {
        return $this->ammunition()->sum('inventory');
    }
}
