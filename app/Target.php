<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }
}
