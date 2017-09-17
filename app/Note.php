<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public function noteable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
