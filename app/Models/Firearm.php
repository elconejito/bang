<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Traits\TransformableTrait;

class Firearm extends Model
{
    use BelongsToUser, HasNotes, TransformableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.firearms';

    protected $fillable = [
        'manufacturer',
        'model',
        'label',
        'user_id',
    ];

    public function calibers(): BelongsToMany
    {
        return $this->belongsToMany(Caliber::class, 'cms.caliber_firearm');
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function totalRoundsFired() {
        return TrainingSession::where('firearm_id', $this->id)
                              ->sum('rounds');
    }
}
