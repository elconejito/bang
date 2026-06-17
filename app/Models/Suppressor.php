<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $manufacturer
 * @property string $label
 * @property string|null $serial
 * @property int|null $caliber_id
 * @property bool $is_nfa
 * @property string|null $mount_type
 * @property string|null $nfa_form_type
 * @property Carbon|null $nfa_approved_date
 * @property string|null $nfa_trust
 * @property int|null $firearm_id
 * @property int|null $location_id
 * @property Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property int|null $purchase_store_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Caliber|null $caliber
 */
class Suppressor extends Accessory
{
    protected $table = 'cms.suppressors';

    protected $fillable = [
        'manufacturer',
        'label',
        'serial',
        'caliber_id',
        'is_nfa',
        'mount_type',
        'nfa_form_type',
        'nfa_approved_date',
        'nfa_trust',
        'firearm_id',
        'location_id',
        'purchase_date',
        'purchase_price',
        'purchase_store_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'is_nfa' => 'boolean',
            'nfa_approved_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Caliber, self>
     */
    public function caliber(): BelongsTo
    {
        return $this->belongsTo(Caliber::class);
    }

    /**
     * @return HasMany<SessionLine, self>
     */
    public function sessionLines(): HasMany
    {
        return $this->hasMany(SessionLine::class);
    }

    /**
     * Sum of rounds across session lines with add_suppressor_count enabled.
     */
    public function totalRoundsFired(): int
    {
        return (int) SessionLine::where('suppressor_id', $this->id)
            ->where('add_suppressor_count', true)
            ->sum('rounds');
    }
}
