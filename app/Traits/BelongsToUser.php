<?php

namespace App\Traits;

use App\Models\User;
use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
