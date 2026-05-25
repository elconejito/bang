<?php

namespace App\Models;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, Note> $notes
 */
class Store extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.stores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'description',
        'user_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return HasMany<Order, self>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return MorphMany<Note, self>
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
