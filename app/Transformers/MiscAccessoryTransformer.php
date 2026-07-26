<?php

namespace App\Transformers;

use App\Models\MiscAccessory;
use League\Fractal\TransformerAbstract;

class MiscAccessoryTransformer extends TransformerAbstract
{
    /**
     * @param  MiscAccessory  $misc
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   sub_type: string|null,
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
    public function transform(MiscAccessory $misc): array
    {
        $misc->loadMissing(['color', 'firearm', 'location', 'purchaseStore', 'pictures']);

        $primaryPicture = $misc->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $misc->pictures->first();

        $thumbnails = $misc->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $misc->id,
            'type' => 'misc',
            'status' => $misc->isArchived() ? 'archived' : 'active',
            'archived_at' => $misc->archived_at?->toISOString(),
            'archive_reason' => $misc->archive_reason?->value,
            'archive_description' => $misc->archive_description,
            'manufacturer' => $misc->manufacturer,
            'label' => $misc->label,
            'serial' => $misc->serial,
            'color_id' => $misc->color_id,
            'color' => $misc->color?->only(['id', 'label']),
            'sub_type' => $misc->sub_type,
            'firearm_id' => $misc->firearm_id,
            'firearm' => $misc->firearm
                ? $misc->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'location_id' => $misc->location_id,
            'location' => $misc->location
                ? $misc->location->only(['id', 'label', 'full_label'])
                : null,
            'purchase_date' => $misc->purchase_date?->toDateString(),
            'purchase_price' => $misc->purchase_price,
            'purchase_store_id' => $misc->purchase_store_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $misc->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $misc->created_at->toISOString(),
            'updated_at' => $misc->updated_at->toISOString(),
        ];
    }
}
