<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class Firearm extends Model
{
    use BelongsToUser, HasNotes, TransformableTrait;

    protected $fillable = [
        'manufacturer',
        'model',
        'label',
        'user_id',
    ];

    public function calibers() {
        return $this->belongsToMany(Caliber::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function targets() {
        return $this->hasMany(Target::class);
    }

    public function totalRoundsFired() {
        return TrainingSession::where('firearm_id', $this->id)
                              ->sum('rounds');
    }
}
