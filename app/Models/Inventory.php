<?php

namespace App\Models;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rounds
 * @property Carbon $inventory_date
 * @property int|null $order_id
 * @property float $cost
 * @property int|null $training_session_id
 * @property int $ammunition_id
 * @property int|null $firearm_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Order|null $order
 * @property-read Ammunition $bullet
 * @property-read TrainingSession|null $trainingSession
 * @property-read Collection<int, Note> $notes
 */
class Inventory extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.inventories';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inventory_date' => 'date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_date',
        'rounds',
        'cost',
        'ammunition_id',
        'order_id',
        'session_line_id',
        'user_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope);
    }

    /**
     * @return BelongsTo<Order, self>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function bullet(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class, 'ammunition_id');
    }

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class, 'ammunition_id');
    }

    public function getCostPerRound(): float
    {
        return $this->cost_per_box / $this->rounds_per_box;
    }

    /**
     * @return BelongsTo<TrainingSession, self>
     */
    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    /**
     * @return BelongsTo<SessionLine, self>
     */
    public function sessionLine(): BelongsTo
    {
        return $this->belongsTo(SessionLine::class);
    }
}
