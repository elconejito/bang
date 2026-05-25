<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.orders';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'order_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'order_date',
        'rounds',
        'total_cost',
        'store_id',
        'user_id',
    ];

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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getRounds(): int
    {
        if ($this->rounds != 0) {
            return $this->rounds;
        }

        return $this->inventories()->sum('rounds');
    }

    public function getTotalCost(): string
    {
        $cost = $this->total_cost != 0.00
            ? $this->total_cost
            : $this->inventories()->sum('cost');

        return '$'.number_format($cost, 2);
    }

    public function updateCost(): void
    {
        $this->total_cost = $this->inventories()->sum('cost');
    }

    public function updateRounds(): void
    {
        $this->rounds = $this->inventories()->sum('rounds');
    }
}
