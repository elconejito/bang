<?php

namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderAsset extends Model
{
    protected $table = 'cms.order_assets';

    protected $fillable = ['order_id', 'asset_type', 'asset_id', 'user_id', 'cost'];

    protected function casts(): array
    {
        return ['cost' => 'decimal:2'];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new UserScope);
    }

    /** @return BelongsTo<Order, self> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return MorphTo<Model, self> */
    public function asset(): MorphTo
    {
        return $this->morphTo();
    }
}
