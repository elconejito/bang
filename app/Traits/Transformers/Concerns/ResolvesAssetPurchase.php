<?php

namespace App\Traits\Transformers\Concerns;

use App\Models\OrderAsset;
use Illuminate\Database\Eloquent\Model;

trait ResolvesAssetPurchase
{
    /** @return array{purchase_date: string|null, purchase_price: float|null, purchase_store_id: int|null, purchase_store: array{id: int, label: string}|null, purchase_order_id: int|null, purchase_order: array{id: int, order_ref: string|null, order_date: string}|null} */
    protected function purchaseData(Model $asset): array
    {
        /** @var OrderAsset|null $orderAsset */
        $orderAsset = $asset->orderAsset;
        $order = $orderAsset?->order;

        return [
            'purchase_date' => $order?->order_date?->toDateString(),
            'purchase_price' => $orderAsset ? (float) $orderAsset->cost : null,
            'purchase_store_id' => $order?->store_id,
            'purchase_store' => $order?->store?->only(['id', 'label']),
            'purchase_order_id' => $order?->id,
            'purchase_order' => $order ? [
                'id' => $order->id,
                'order_ref' => $order->order_ref,
                'order_date' => $order->order_date->toDateString(),
            ] : null,
        ];
    }
}
