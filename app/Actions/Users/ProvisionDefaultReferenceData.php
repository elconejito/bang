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
        ['caliber' => '.22LR', 'label' => '.22LR', 'type' => 'Rimfire'],
        ['caliber' => '5.56×45mm NATO', 'label' => '5.56', 'type' => 'Centerfire'],
        ['caliber' => '9mm Luger', 'label' => '9mm', 'type' => 'Centerfire'],
        ['caliber' => '12 Gauge', 'label' => '12G', 'type' => 'Shotgun'],
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
