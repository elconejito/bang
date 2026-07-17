<?php

namespace App\Models;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $label
 * @property float $distance
 * @property float $group_size
 * @property int $picture_id
 * @property int|null $bullet_id
 * @property int|null $firearm_id
 * @property int $training_session_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Picture $picture
 * @property-read Firearm|null $firearm
 * @property-read Ammunition|null $bullet
 * @property-read TrainingSession $trainingSession
 * @property-read Collection<int, Note> $notes
 */
class Target extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.targets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'distance',
        'group_size',
        'picture_id',
        'firearm_id',
        'bullet_id',
        'user_id',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return BelongsTo<Picture, self>
     */
    public function picture(): BelongsTo
    {
        return $this->belongsTo(Picture::class);
    }

    /**
     * @return BelongsTo<TrainingSession, self>
     */
    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    /**
     * @return BelongsTo<Firearm, self>
     */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function bullet(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }
}
