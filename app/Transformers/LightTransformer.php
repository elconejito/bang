<?php

namespace App\Transformers;

use App\Models\Light;
use League\Fractal\TransformerAbstract;

class LightTransformer extends TransformerAbstract
{
    /**
     * @param  Light  $light
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   lumens: int|null,
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
    public function transform(Light $light): array
    {
        $light->loadMissing(['firearm', 'location', 'purchaseStore', 'pictures']);

        $primaryPicture = $light->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $light->pictures->first();

        $thumbnails = $light->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $light->id,
            'type' => 'light',
            'manufacturer' => $light->manufacturer,
            'label' => $light->label,
            'serial' => $light->serial,
            'lumens' => $light->lumens,
            'battery_type' => $light->battery_type,
            'firearm_id' => $light->firearm_id,
            'firearm' => $light->firearm
                ? $light->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'location_id' => $light->location_id,
            'location' => $light->location
                ? $light->location->only(['id', 'label'])
                : null,
            'purchase_date' => $light->purchase_date?->toDateString(),
            'purchase_price' => $light->purchase_price,
            'purchase_store_id' => $light->purchase_store_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $light->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $light->created_at->toISOString(),
            'updated_at' => $light->updated_at->toISOString(),
        ];
    }
}
