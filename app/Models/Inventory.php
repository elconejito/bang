<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms.inventories';

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
     *
     * @return void
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

    public function bullet() {
        return $this->belongsTo(Ammunition::class);
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function getCostPerRound() {
        $cost = $this->cost_per_box / $this->rounds_per_box;

        return $cost;
    }
}
