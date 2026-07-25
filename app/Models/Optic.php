<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $manufacturer
 * @property string $label
 * @property string|null $serial
 * @property string|null $optic_type
 * @property string|null $battery_type
 * @property int|null $firearm_id
 * @property int|null $location_id
 * @property Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property int|null $purchase_store_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Optic extends Accessory
{
    protected $table = 'cms.optics';

    protected $fillable = [
        'manufacturer',
        'label',
        'serial',
        'optic_type',
        'battery_type',
        'color_id',
        'firearm_id',
        'location_id',
        'purchase_date',
        'purchase_price',
        'purchase_store_id',
        'user_id',
    ];
}
