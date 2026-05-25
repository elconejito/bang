<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Target extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.targets';

    protected $fillable = [
        'label',
        'distance',
        'group_size',
        'picture_id',
        'trip_id',
        'shoot_id',
        'firearm_id',
        'bullet_id',
        'user_id',
    ];

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

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function picture(): BelongsTo
    {
        return $this->belongsTo(Picture::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function shoot(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }

    public function bullet(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }
}
