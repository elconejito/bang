<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    protected $fillable = [
        'user_id',
        'note',
    ];

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
