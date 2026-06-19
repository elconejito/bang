<?php

namespace App\Transformers;

use App\Models\Suppressor;
use League\Fractal\TransformerAbstract;

class SuppressorTransformer extends TransformerAbstract
{
    /**
     * @param  Suppressor  $suppressor
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   caliber_id: int|null,
     *   caliber: array{id: int, label: string}|null,
     *   is_nfa: bool,
     *   mount_type: string|null,
     *   nfa_form_type: string|null,
     *   nfa_approved_date: string|null,
     *   nfa_trust: string|null,
     *   rounds_fired: int,
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
    public function transform(Suppressor $suppressor): array
    {
        $suppressor->loadMissing(['caliber', 'firearm', 'location', 'purchaseStore']);

        return [
            'id' => $suppressor->id,
            'type' => 'suppressor',
            'manufacturer' => $suppressor->manufacturer,
            'label' => $suppressor->label,
            'serial' => $suppressor->serial,
            'caliber_id' => $suppressor->caliber_id,
            'caliber' => $suppressor->caliber
                ? $suppressor->caliber->only(['id', 'label'])
                : null,
            'is_nfa' => $suppressor->is_nfa,
            'mount_type' => $suppressor->mount_type,
            'nfa_form_type' => $suppressor->nfa_form_type,
            'nfa_approved_date' => $suppressor->nfa_approved_date?->toDateString(),
            'nfa_trust' => $suppressor->nfa_trust,
            'rounds_fired' => $suppressor->totalRoundsFired(),
            'firearm_id' => $suppressor->firearm_id,
            'firearm' => $suppressor->firearm
                ? $suppressor->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'location_id' => $suppressor->location_id,
            'location' => $suppressor->location
                ? $suppressor->location->only(['id', 'label'])
                : null,
            'purchase_date' => $suppressor->purchase_date?->toDateString(),
            'purchase_price' => $suppressor->purchase_price,
            'purchase_store_id' => $suppressor->purchase_store_id,
            'created_at' => $suppressor->created_at->toISOString(),
            'updated_at' => $suppressor->updated_at->toISOString(),
        ];
    }
}
