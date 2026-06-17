<?php

namespace App\Transformers;

use App\Models\MiscAccessory;
use League\Fractal\TransformerAbstract;

class MiscAccessoryTransformer extends TransformerAbstract
{
    /**
     * @param  MiscAccessory  $misc
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   sub_type: string|null,
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
    public function transform(MiscAccessory $misc): array
    {
        $misc->loadMissing(['firearm', 'location', 'purchaseStore']);

        return [
            'id' => $misc->id,
            'type' => 'misc',
            'manufacturer' => $misc->manufacturer,
            'label' => $misc->label,
            'serial' => $misc->serial,
            'sub_type' => $misc->sub_type,
            'firearm_id' => $misc->firearm_id,
            'firearm' => $misc->firearm
                ? $misc->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'location_id' => $misc->location_id,
            'location' => $misc->location
                ? $misc->location->only(['id', 'label'])
                : null,
            'purchase_date' => $misc->purchase_date?->toDateString(),
            'purchase_price' => $misc->purchase_price,
            'purchase_store_id' => $misc->purchase_store_id,
            'created_at' => $misc->created_at->toISOString(),
            'updated_at' => $misc->updated_at->toISOString(),
        ];
    }
}
