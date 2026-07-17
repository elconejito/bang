<?php

namespace App\Actions\Users;

use App\Models\Caliber;
use App\Models\Reference\CaliberType;
use App\Models\Reference\Purpose;
use App\Models\User;
use LogicException;

class ProvisionDefaultReferenceData
{
    private const PURPOSES = [
        'Range/Training',
        'Home/Self Defense',
        'Match/Competition',
        'Hunting',
    ];

    private const CALIBERS = [
        ['caliber' => '.22 Long Rifle', 'label' => '.22LR', 'type' => 'Rimfire'],
        ['caliber' => '38 Special', 'label' => '.38 Spl', 'type' => 'Centerfire'],
        ['caliber' => '357 Magnum', 'label' => '.357 Mag', 'type' => 'Centerfire'],
        ['caliber' => '380 Automatic', 'label' => '.380', 'type' => 'Centerfire'],
        ['caliber' => '9mm Luger', 'label' => '9mm', 'type' => 'Centerfire'],
        ['caliber' => '10mm Automatic', 'label' => '10mm', 'type' => 'Centerfire'],
        ['caliber' => '45 Automatic', 'label' => '.45 ACP', 'type' => 'Centerfire'],
        ['caliber' => '12-Gauge', 'label' => '12G', 'type' => 'Shotgun'],
        ['caliber' => '20-Gauge', 'label' => '20G', 'type' => 'Shotgun'],
        ['caliber' => '.223 Remington', 'label' => '.223', 'type' => 'Centerfire'],
        ['caliber' => '5.56×45mm NATO', 'label' => '5.56', 'type' => 'Centerfire'],
        ['caliber' => '300 AAC Blackout', 'label' => '300BLK', 'type' => 'Centerfire'],
        ['caliber' => '308 Winchester', 'label' => '.308', 'type' => 'Centerfire'],
        ['caliber' => '30-06 Springfield', 'label' => '30-06', 'type' => 'Centerfire'],
        ['caliber' => '6.5 Creedmoor', 'label' => '6.5 Creedmoor', 'type' => 'Centerfire'],
        ['caliber' => '7.62 x 39', 'label' => '7.62 x 39', 'type' => 'Centerfire'],
    ];

    public function execute(User $user): void
    {
        foreach (self::PURPOSES as $label) {
            Purpose::withoutGlobalScopes()
                ->withTrashed()
                ->firstOrCreate(['user_id' => $user->id, 'label' => $label]);
        }

        $caliberTypes = CaliberType::query()
            ->whereIn('label', collect(self::CALIBERS)->pluck('type')->unique())
            ->pluck('id', 'label');

        foreach (self::CALIBERS as $caliber) {
            $caliberTypeId = $caliberTypes->get($caliber['type']);

            if ($caliberTypeId === null) {
                throw new LogicException("Missing default caliber type: {$caliber['type']}");
            }

            Caliber::withoutGlobalScopes()
                ->withTrashed()
                ->firstOrCreate(
                    ['user_id' => $user->id, 'caliber' => $caliber['caliber']],
                    ['label' => $caliber['label'], 'caliber_type_id' => $caliberTypeId]
                );
        }
    }
}
