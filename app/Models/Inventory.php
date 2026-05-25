<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Inventory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    use HasFactory;

    protected $table = 'cms.inventories';

    protected $casts = [
        'inventory_date' => 'date',
    ];

    protected $fillable = [
        'inventory_date',
        'rounds',
        'cost',
        'ammunition_id',
        'order_id',
        'user_id',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bullet(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getCostPerRound(): float
    {
        return $this->cost_per_box / $this->rounds_per_box;
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }
}
