<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
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
        return $this->morphedByMany(Target::class, 'pictureable');
    }

    public function trips() {
        return $this->morphedByMany(Trip::class, 'pictureable');
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
