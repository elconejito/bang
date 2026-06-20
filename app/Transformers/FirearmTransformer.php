<?php

namespace App\Transformers;

use App\Models\Firearm;
use League\Fractal\TransformerAbstract;

class FirearmTransformer extends TransformerAbstract
{
    /**
     * @return array{
     *   id: int,
     *   label: string|null,
     *   manufacturer: string,
     *   model: string|null,
     *   serial: string|null,
     *   location_id: int|null,
     *   location: array{id: int, label: string}|null,
     *   purchase_date: string|null,
     *   purchase_price: string|null,
     *   purchase_store_id: int|null,
     *   purchase_store: array{id: int, label: string}|null,
     *   calibers: array<int, array{id: int, label: string}>,
     *   rounds_fired: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Firearm $firearm): array
    {
        $firearm->loadMissing(['calibers', 'location', 'purchaseStore', 'pictures']);

        $primaryPicture = $firearm->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $firearm->pictures->first();

        $thumbnails = $firearm->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()
            ->all();

        return [
            'id' => $firearm->id,
            'label' => $firearm->label,
            'manufacturer' => $firearm->manufacturer,
            'model' => $firearm->model,
            'serial' => $firearm->serial,
            'location_id' => $firearm->location_id,
            'location' => $firearm->location
                ? ['id' => $firearm->location->id, 'label' => $firearm->location->label]
                : null,
            'purchase_date' => $firearm->purchase_date?->toDateString(),
            'purchase_price' => $firearm->purchase_price,
            'purchase_store_id' => $firearm->purchase_store_id,
            'purchase_store' => $firearm->purchaseStore
                ? ['id' => $firearm->purchaseStore->id, 'label' => $firearm->purchaseStore->label]
                : null,
            'calibers' => $firearm->calibers->map(fn ($c) => [
                'id' => $c->id,
                'label' => $c->label,
            ])->all(),
            'rounds_fired' => $firearm->totalRoundsFired(),
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $firearm->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $firearm->created_at->toISOString(),
            'updated_at' => $firearm->updated_at->toISOString(),
        ];
    }
}
