<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $training_session_id
 * @property int $firearm_id
 * @property int $ammunition_id
 * @property int|null $suppressor_id
 * @property int $rounds
 * @property bool $deduct_ammo
 * @property bool $add_firearm_count
 * @property bool $add_suppressor_count
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read TrainingSession $trainingSession
 * @property-read Firearm $firearm
 * @property-read Ammunition $ammunition
 * @property-read Suppressor|null $suppressor
 * @property-read Inventory|null $inventoryDeduction
 */
class SessionLine extends Model
{
    use BelongsToUser, HasFactory;

    /**
     * @var string
     */
    protected $table = 'cms.session_lines';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'training_session_id',
        'firearm_id',
        'ammunition_id',
        'suppressor_id',
        'rounds',
        'deduct_ammo',
        'add_firearm_count',
        'add_suppressor_count',
        'user_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'deduct_ammo' => 'boolean',
        'add_firearm_count' => 'boolean',
        'add_suppressor_count' => 'boolean',
    ];

    /**
     * @return BelongsTo<TrainingSession, self>
     */
    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    /**
     * @return BelongsTo<Firearm, self>
     */
    public function firearm(): BelongsTo
    {
        return $this->belongsTo(Firearm::class);
    }

    /**
     * @return BelongsTo<Ammunition, self>
     */
    public function ammunition(): BelongsTo
    {
        return $this->belongsTo(Ammunition::class);
    }

    /**
     * @return BelongsTo<Suppressor, self>
     */
    public function suppressor(): BelongsTo
    {
        return $this->belongsTo(Suppressor::class);
    }

    /**
     * @return HasOne<Inventory, self>
     */
    public function inventoryDeduction(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
