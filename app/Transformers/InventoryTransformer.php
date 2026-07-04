<?php

namespace App\Transformers;

use App\Models\Inventory;
use League\Fractal\TransformerAbstract;

class InventoryTransformer extends TransformerAbstract
{
    /**
     * @param  Inventory  $inventory
     * @return array{
     *   id: int,
     *   type: string,
     *   inventory_date: string,
     *   rounds: int,
     *   cost: float,
     *   training_session_id: int|null,
     *   training_session_label: string|null,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Inventory $inventory): array
    {
        $type = match (true) {
            $inventory->order_id !== null => 'BUY',
            $inventory->session_line_id !== null => 'FIRED',
            default => 'ADJUST',
        };

        $trainingSessionId = null;
        $trainingSessionLabel = null;

        if ($inventory->session_line_id !== null && $inventory->relationLoaded('sessionLine') && $inventory->sessionLine) {
            $session = $inventory->sessionLine->trainingSession;
            $trainingSessionId = $session?->id;
            $trainingSessionLabel = $session?->label;
        }

        $storeLabel = null;
        if ($inventory->order_id !== null && $inventory->relationLoaded('order') && $inventory->order) {
            $storeLabel = $inventory->order->store?->label;
        }

        return [
            'id' => $inventory->id,
            'type' => $type,
            'inventory_date' => $inventory->inventory_date->toDateString(),
            'rounds' => $inventory->rounds,
            'cost' => (float) $inventory->cost,
            'store_label' => $storeLabel,
            'training_session_id' => $trainingSessionId,
            'training_session_label' => $trainingSessionLabel,
            'created_at' => $inventory->created_at->toISOString(),
            'updated_at' => $inventory->updated_at->toISOString(),
        ];
    }
}
