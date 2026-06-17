<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $manufacturer
 * @property string $label
 * @property string|null $serial
 * @property int|null $lumens
 * @property string|null $battery_type
 * @property int|null $firearm_id
 * @property int|null $location_id
 * @property Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property int|null $purchase_store_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Light extends Accessory
{
    protected $table = 'cms.lights';

    protected $fillable = [
        'manufacturer',
        'label',
        'serial',
        'lumens',
        'battery_type',
        'firearm_id',
        'location_id',
        'purchase_date',
        'purchase_price',
        'purchase_store_id',
        'user_id',
    ];
}
