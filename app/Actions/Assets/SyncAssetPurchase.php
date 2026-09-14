<?php

namespace App\Actions\Assets;

use App\Models\Accessory;
use App\Models\Firearm;
use App\Models\Magazine;
use App\Models\Order;
use App\Models\OrderAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncAssetPurchase
{
    /** @param array{order_id?: int|null, cost?: numeric-string|float|int|null} $data */
    public function execute(Firearm|Accessory|Magazine $asset, array $data, int $userId): void
    {
        if (! array_key_exists('order_id', $data)) {
            return;
        }

        DB::transaction(function () use ($asset, $data, $userId): void {
            $lockedAsset = $asset->newQuery()->whereKey($asset)->where('user_id', $userId)->lockForUpdate()->firstOrFail();
            $link = OrderAsset::query()
                ->where('asset_type', $lockedAsset->getMorphClass())
                ->where('asset_id', $lockedAsset->getKey())
                ->lockForUpdate()
                ->first();
            $previousOrderId = $link?->order_id;

            if ($data['order_id'] === null) {
                $previousOrder = $previousOrderId
                    ? Order::query()->whereKey($previousOrderId)->where('user_id', $userId)->lockForUpdate()->first()
                    : null;
                $link?->delete();
                $previousOrder?->recalculateTotals();
                $asset->unsetRelation('orderAsset');

                return;
            }

            $orderIds = collect([$previousOrderId, (int) $data['order_id']])->filter()->unique()->sort()->values();
            $orders = Order::query()->whereIn('id', $orderIds)->where('user_id', $userId)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $order = $orders->get((int) $data['order_id']);
            if (! $order instanceof Order) {
                throw ValidationException::withMessages(['order_id' => 'The selected order is invalid.']);
            }
            $previousOrder = $previousOrderId ? $orders->get($previousOrderId) : null;

            $link ??= new OrderAsset([
                'asset_type' => $lockedAsset->getMorphClass(),
                'asset_id' => $lockedAsset->getKey(),
            ]);
            $link->fill([
                'order_id' => $order->id,
                'user_id' => $userId,
                'cost' => $data['cost'] ?? 0,
            ])->save();
            if ($previousOrder?->id !== $order->id) {
                $previousOrder?->recalculateTotals();
            }
            $order->recalculateTotals();
            $asset->unsetRelation('orderAsset');
        }, attempts: 3);
    }
}
