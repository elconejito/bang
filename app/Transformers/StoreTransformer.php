<?php

namespace App\Transformers;

use App\Models\Store;
use League\Fractal\TransformerAbstract;

class StoreTransformer extends TransformerAbstract
{
    /**
     * @param  Store  $store
     * @return array{
     *   id: int,
     *   label: string,
     *   description: string|null,
     *   user_id: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Store $store): array
    {
        $store->loadMissing(['pictures', 'orders' => fn ($query) => $query
            ->withCount(['inventories', 'orderAssets'])
            ->withSum('inventories', 'rounds')]);

        $primaryPicture = $store->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $store->pictures->first();

        $thumbnails = $store->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        $orders = $store->orders->sortByDesc('order_date')->values();

        return [
            'id' => $store->id,
            'label' => $store->label,
            'description' => $store->description,
            'user_id' => $store->user_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $store->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'orders_count' => $orders->count(),
            'total_rounds' => $orders->sum(fn ($o) => (int) ($o->inventories_sum_rounds ?? $o->rounds)),
            'total_spent' => $orders->sum(fn ($o) => (float) $o->total_cost),
            'orders' => $orders->map(fn ($o) => [
                'id' => $o->id,
                'order_date' => $o->order_date->toDateString(),
                'order_ref' => $o->order_ref,
                'rounds' => (int) ($o->inventories_sum_rounds ?? $o->rounds),
                'items_count' => $o->inventories_count + $o->order_assets_count,
                'total_cost' => (float) $o->total_cost,
            ])->values()->all(),
            'created_at' => $store->created_at->toISOString(),
            'updated_at' => $store->updated_at->toISOString(),
        ];
    }
}
