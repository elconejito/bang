<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Note
 *
 * This is a Laravel One to Many Polymorphic relationship. Each related model may have one or more Notes.
 *
 * @package App\Models
 */
class Note extends Model
{
    use BelongsToUser;

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

    public function notable() {
        return $this->morphTo();
    }
}
