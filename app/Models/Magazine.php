<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Traits\TransformableTrait;

class Magazine extends Model
{
    use BelongsToUser, HasNotes, TransformableTrait;

    protected $fillable = [
        'label',
        'manufacturer',
        'model_name',
        'capacity',
        'serial_number',
        'id_marking',
        'user_id',
    ];


    public function pictures() {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    public function calibers() {
        return $this->belongsToMany(Caliber::class);
    }

    public function firearms() {
        return $this->belongsToMany(Firearm::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
