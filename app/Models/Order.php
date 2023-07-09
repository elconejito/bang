<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * @return BelongsTo
     */
    public function store() {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return HasMany
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getRounds() {
        if ( $this->rounds != 0 ) {
            $rounds = $this->rounds;
        } else {
            $rounds = $this->inventories()->sum('rounds');
        }
        return $rounds;
    }

    public function getTotalCost() {
        if ( $this->total_cost != 0.00 ) {
            $cost = $this->total_cost;
        } else {
            $cost = $this->inventories()->sum('cost');
        }
        return "$" . number_format($cost, 2);
    }

    public function updateCost() {
        $this->total_cost = $this->inventories()->sum('cost');
    }

    public function updateRounds() {
        $this->rounds = $this->inventories()->sum('rounds');
    }
}
