<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string|null $label
 * @property string $manufacturer
 * @property string|null $model
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Caliber> $calibers
 * @property-read Collection<int, Picture> $pictures
 * @property-read Collection<int, Target> $targets
 */
class Firearm extends Model
{
    use BelongsToUser, HasFactory, HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.firearms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manufacturer',
        'model',
        'label',
        'user_id',
    ];

    /**
     * @return BelongsToMany<Caliber, self>
     */
    public function calibers(): BelongsToMany
    {
        return $this->belongsToMany(Caliber::class, 'cms.caliber_firearm');
    }

    /**
     * @return HasMany<Picture, self>
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    /**
     * @return HasMany<Target, self>
     */
    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function totalRoundsFired(): int
    {
        return TrainingSession::where('firearm_id', $this->id)
            ->sum('rounds');
    }
}
