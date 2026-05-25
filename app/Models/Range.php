<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Range extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.ranges';

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

    public function shoots(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
