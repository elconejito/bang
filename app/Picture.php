<?php

namespace App;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Picture extends Model
{
    protected $fillable = [
        'name',
        'filename',
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


    public function resize() {
        // dd(storage_path('app/public/images/' . $this->filename));
        $img = Image::make(storage_path('app/public/images/' . $this->filename));
        // save Large
        $img->fit(1920, 1440);
        $img->save(storage_path('app/public/images/large/' . $this->filename));

        // save Medium
        $img->fit(480, 360);
        $img->save(storage_path('app/public/images/medium/' . $this->filename));

        // Save Thumbnail
        $img->fit(220, 165);
        $img->save(storage_path('app/public/images/thumbnail/' . $this->filename));
    }

    public function getPath($size = 'thumbnail') {
        return 'storage/images/' . $size . '/' . $this->filename;
    }

    public function bullets() {
        return $this->morphedByMany(Bullet::class, 'pictureable');
    }

    public function firearms() {
        return $this->morphedByMany(Firearm::class, 'pictureable');
    }

    public function inventories() {
        return $this->morphedByMany(Inventory::class, 'pictureable');
    }

    public function magazines() {
        return $this->morphedByMany(Magazine::class, 'pictureable');
    }

    public function orders() {
        return $this->morphedByMany(Order::class, 'pictureable');
    }

    public function ranges() {
        return $this->morphedByMany(Range::class, 'pictureable');
    }

    public function shoots() {
        return $this->morphedByMany(Shoot::class, 'pictureable');
    }

    public function stores() {
        return $this->morphedByMany(Store::class, 'pictureable');
    }

    public function targets() {
        return $this->belongsToMany(Target::class);
    }

    public function trips() {
        return $this->morphedByMany(Trip::class, 'pictureable');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
