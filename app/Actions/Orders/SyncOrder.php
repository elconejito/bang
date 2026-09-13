<?php

namespace App\Actions\Orders;

use App\Enums\OrderAssetType;
use App\Models\Ammunition;
use App\Models\Order;
use App\Models\OrderAsset;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncOrder
{
    /** @param array<string, mixed> $data */
    public function execute(?Order $order, array $data, int $userId): Order
    {
        return DB::transaction(function () use ($order, $data, $userId): Order {
            $order ??= new Order(['user_id' => $userId]);
            if ($order->exists) {
                $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            }
            $order->fill(Arr::only($data, ['store_id', 'order_date', 'order_ref']))->forceFill(['rounds' => 0, 'total_cost' => 0, 'user_id' => $userId])->save();
            $existing = $order->inventories()->get()->keyBy('id');
            $ammoIds = $existing->pluck('ammunition_id');
            $retained = collect();
            foreach ($data['items'] as $index => $item) {
                if (($item['type'] ?? 'ammunition') !== 'ammunition') {
                    continue;
                }
                $ammoIds->push($item['ammunition_id']);
                $inventory = isset($item['id']) ? $existing->get($item['id']) : $order->inventories()->make();
                if (! $inventory) {
                    throw ValidationException::withMessages(['items' => 'Invalid order item.']);
                }
                $inventory->fill(['ammunition_id' => $item['ammunition_id'], 'inventory_date' => $data['order_date'], 'rounds' => $item['rounds'], 'cost' => $item['cost'] ?? 0, 'user_id' => $userId])->save();
                $retained->push($inventory->id);
            }
            $order->inventories()->whereNotIn('id', $retained)->delete();
            $assetRetained = collect();
            foreach ($data['items'] as $index => $item) {
                if (($item['type'] ?? 'ammunition') === 'ammunition') {
                    continue;
                }
                $type = OrderAssetType::tryFrom($item['type']);
                $class = $type ? OrderAssetType::models()[$type->value] : null;
                $asset = $class ? $class::whereKey($item['asset_id'])->where('user_id', $userId)->lockForUpdate()->first() : null;
                if (! $asset) {
                    throw ValidationException::withMessages(['items' => 'Asset is invalid or not owned by you.']);
                }
                $link = OrderAsset::withoutGlobalScopes()->where('asset_type', $class)->where('asset_id', $asset->id)->lockForUpdate()->first();
                if (isset($item['id']) && (! $link || $link->id !== (int) $item['id'] || $link->order_id !== $order->id)) {
                    throw ValidationException::withMessages(["items.$index.id" => 'This asset line does not belong to this order.']);
                }
                if ($link && $link->order_id !== $order->id) {
                    throw ValidationException::withMessages(['items' => 'Asset already belongs to another order.']);
                }
                $link ??= new OrderAsset(['asset_type' => $class, 'asset_id' => $asset->id]);
                $link->fill(['order_id' => $order->id, 'user_id' => $userId, 'cost' => $item['cost'] ?? 0])->save();
                $assetRetained->push($link->id);
            }
            $order->orderAssets()->whereNotIn('id', $assetRetained)->delete();
            $order->recalculateTotals();
            Ammunition::whereIn('id', $ammoIds->unique())->get()->each(fn (Ammunition $a) => $a->recalculateInventory());

            return $order->refresh()->load(['store', 'inventories.ammunition.caliber', 'orderAssets.asset']);
        }, attempts: 3);
    }
}
