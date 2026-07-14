<?php

namespace App\Actions\Orders;

use App\Models\Ammunition;
use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SyncOrder
{
    /** @param array{store_id?: int|null, order_date: string, order_ref?: string|null, items: array<int, array{id?: int|null, ammunition_id: int, rounds: int, cost?: numeric-string|float|int|null}>} $data */
    public function execute(?Order $order, array $data, int $userId): Order
    {
        return DB::transaction(function () use ($order, $data, $userId): Order {
            $order ??= new Order(['user_id' => $userId]);
            $order->fill(Arr::only($data, ['store_id', 'order_date', 'order_ref']));
            $order->forceFill(['rounds' => 0, 'total_cost' => 0, 'user_id' => $userId])->save();

            $existingItems = $order->inventories()->get()->keyBy('id');
            $affectedAmmunitionIds = $existingItems->pluck('ammunition_id')
                ->merge(collect($data['items'])->pluck('ammunition_id'))
                ->unique();
            $retainedItemIds = collect();

            foreach ($data['items'] as $itemData) {
                $inventory = isset($itemData['id'])
                    ? $existingItems->get($itemData['id'])
                    : $order->inventories()->make(['user_id' => $userId]);

                $inventory->fill([
                    'ammunition_id' => $itemData['ammunition_id'],
                    'inventory_date' => $data['order_date'],
                    'rounds' => $itemData['rounds'],
                    'cost' => $itemData['cost'] ?? 0,
                    'user_id' => $userId,
                ])->save();

                $retainedItemIds->push($inventory->getKey());
            }

            $order->inventories()->whereNotIn('id', $retainedItemIds)->delete();
            $order->recalculateTotals();

            Ammunition::whereIn('id', $affectedAmmunitionIds)
                ->get()
                ->each(fn (Ammunition $ammunition) => $ammunition->recalculateInventory());

            return $order->refresh()->load(['store', 'inventories.ammunition.caliber']);
        });
    }
}
