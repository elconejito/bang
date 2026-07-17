<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property string|null $address
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, TrainingSession> $sessions
 * @property-read Collection<int, Note> $notes
 * @property-read Collection<int, Picture> $pictures
 */
class Range extends Model
{
    use HasFactory, HasNotes;

    protected $table = 'cms.ranges';

    protected $fillable = [
        'label',
        'description',
        'address',
        'user_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return HasMany<TrainingSession, self>
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'range_id');
    }

    /**
     * @return MorphToMany<Picture, self>
     */
    public function pictures(): MorphToMany
    {
        return $this->morphToMany(Picture::class, 'pictureable', 'cms.pictureables')
            ->withPivot('sort_order', 'is_primary')
            ->orderByPivot('sort_order');
    }
}
