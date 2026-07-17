<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    /** @return array<string, mixed> */
    public function transform(Order $order): array
    {
        $order->loadMissing(['store', 'inventories.ammunition.caliber']);

        return [
            'id' => $order->id,
            'store_id' => $order->store_id,
            'store' => $order->store ? ['id' => $order->store->id, 'label' => $order->store->label] : null,
            'order_date' => $order->order_date->toDateString(),
            'order_ref' => $order->order_ref,
            'rounds' => (int) $order->rounds,
            'total_cost' => (float) $order->total_cost,
            'items' => $order->inventories->map(fn ($inventory) => [
                'id' => $inventory->id,
                'type' => 'ammunition',
                'ammunition_id' => $inventory->ammunition_id,
                'rounds' => (int) $inventory->rounds,
                'cost' => (float) $inventory->cost,
                'cost_per_round' => $inventory->rounds > 0 ? round((float) $inventory->cost / $inventory->rounds, 4) : null,
                'ammunition' => [
                    'id' => $inventory->ammunition->id,
                    'manufacturer' => $inventory->ammunition->manufacturer,
                    'label' => $inventory->ammunition->label,
                    'caliber' => $inventory->ammunition->caliber ? [
                        'id' => $inventory->ammunition->caliber->id,
                        'label' => $inventory->ammunition->caliber->label,
                    ] : null,
                ],
            ])->values()->all(),
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}
