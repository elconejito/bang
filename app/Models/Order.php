<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\HasNotes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $rounds
 * @property float $total_cost
 * @property int|null $store_id
 * @property string|null $order_ref
 * @property Carbon $order_date
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Store|null $store
 * @property-read Collection<int, Inventory> $inventories
 * @property-read Collection<int, Note> $notes
 */
class Order extends Model
{
    use HasNotes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.orders';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'rounds' => 'integer',
        'total_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_date',
        'rounds',
        'total_cost',
        'store_id',
        'order_ref',
        'user_id',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return BelongsTo<Store, self>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return HasMany<Inventory, self>
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
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
        $this->save();
    }

    public function updateRounds(): void
    {
        $this->rounds = $this->inventories()->sum('rounds');
        $this->save();
    }

    public function recalculateTotals(): void
    {
        $this->forceFill([
            'rounds' => $this->inventories()->sum('rounds'),
            'total_cost' => $this->inventories()->sum('cost'),
        ])->save();
    }
}
