<?php

namespace App\Actions\Firearms;

use App\Models\Accessory;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BatchMountAccessories
{
    /** @var array<string, class-string<Accessory>> */
    private const TYPES = [
        'Suppressor' => Suppressor::class,
        'Optic' => Optic::class,
        'Light' => Light::class,
        'Misc' => MiscAccessory::class,
    ];

    /**
     * @param  array<int, array{type: string, id: int}>  $accessories
     */
    public function execute(Firearm $firearm, array $accessories): void
    {
        DB::transaction(function () use ($firearm, $accessories): void {
            foreach ($accessories as $accessory) {
                $model = $this->findEligibleAccessory($firearm, $accessory);
                $model->update(['firearm_id' => $firearm->id]);
            }
        });
    }

    /**
     * @param  array{type: string, id: int}  $accessory
     */
    private function findEligibleAccessory(Firearm $firearm, array $accessory): Accessory
    {
        $class = self::TYPES[$accessory['type']];

        $model = $class::withoutGlobalScopes()
            ->whereKey($accessory['id'])
            ->where('user_id', $firearm->user_id)
            ->whereNull('archived_at')
            ->whereNull('firearm_id')
            ->lockForUpdate()
            ->first();

        if (! $model instanceof Accessory || ($model instanceof MiscAccessory && $this->isNonMountableMisc($model))) {
            throw ValidationException::withMessages([
                'accessories' => ['One or more selected accessories are unavailable to mount.'],
            ]);
        }

        return $model;
    }

    private function isNonMountableMisc(MiscAccessory $accessory): bool
    {
        return in_array(strtolower((string) $accessory->sub_type), ['holster', 'case', 'bag'], true);
    }
}
