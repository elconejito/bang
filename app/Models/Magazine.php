<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;

class Magazine extends Model
{
    protected $fillable = [
        'label',
        'manufacturer',
        'model_name',
        'capacity',
        'serial_number',
        'id_marking',
        'cartridge_id',
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


    public function pictures() {
        return $this->morphToMany(Picture::class, 'pictureable');
    }

    public function cartridge() {
        return $this->belongsTo(Cartridge::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
