<?php

namespace App\Traits;

use App\Models\Note;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotes
{
    protected static function bootHasNotes(): void
    {
        static::deleting(function (self $notable): void {
            $notable->notes()->withoutGlobalScopes()->delete();
        });
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
