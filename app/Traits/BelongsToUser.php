<?php


namespace App\Traits;

use App\Models\User;
use App\Scopes\UserScope;

trait BelongsToUser
{
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

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
