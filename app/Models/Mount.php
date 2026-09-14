<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $user_id
 * @property string $manufacturer
 * @property string $label
 * @property string|null $model_number
 * @property string|null $serial
 * @property string|null $height
 * @property string|null $mount_type
 * @property int|null $firearm_id
 * @property int|null $location_id
 */
class Mount extends Accessory
{
    protected $table = 'cms.mounts';

    protected $fillable = [
        'manufacturer', 'label', 'model_number', 'serial', 'height', 'mount_type', 'color_id', 'firearm_id',
        'location_id', 'user_id',
    ];
}
