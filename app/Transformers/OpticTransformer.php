<?php

namespace App\Transformers;

use App\Models\Optic;
use League\Fractal\TransformerAbstract;

class OpticTransformer extends TransformerAbstract
{
    /**
     * @param  Optic  $optic
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   optic_type: string|null,
     *   battery_type: string|null,
     *   firearm_id: int|null,
     *   firearm: array{id: int, label: string}|null,
     *   location_id: int|null,
     *   location: array{id: int, label: string}|null,
     *   purchase_date: string|null,
     *   purchase_price: float|null,
     *   purchase_store_id: int|null,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Optic $optic): array
    {
        $optic->loadMissing(['firearm', 'location', 'purchaseStore']);

        return [
            'id' => $optic->id,
            'type' => 'optic',
            'manufacturer' => $optic->manufacturer,
            'label' => $optic->label,
            'serial' => $optic->serial,
            'optic_type' => $optic->optic_type,
            'battery_type' => $optic->battery_type,
            'firearm_id' => $optic->firearm_id,
            'firearm' => $optic->firearm
                ? $optic->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'location_id' => $optic->location_id,
            'location' => $optic->location
                ? $optic->location->only(['id', 'label'])
                : null,
            'purchase_date' => $optic->purchase_date?->toDateString(),
            'purchase_price' => $optic->purchase_price,
            'purchase_store_id' => $optic->purchase_store_id,
            'created_at' => $optic->created_at->toISOString(),
            'updated_at' => $optic->updated_at->toISOString(),
        ];
    }
}
