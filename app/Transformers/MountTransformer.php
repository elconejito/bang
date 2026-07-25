<?php

namespace App\Transformers;

use App\Models\Mount;
use App\Transformers\Concerns\ResolvesMountedSince;
use League\Fractal\TransformerAbstract;

class MountTransformer extends TransformerAbstract
{
    use ResolvesMountedSince;

    public function transform(Mount $mount): array
    {
        $mount->loadMissing(['color', 'firearm', 'location', 'purchaseStore', 'pictures']);

        return [
            'id' => $mount->id, 'type' => 'mount', 'status' => $mount->isArchived() ? 'archived' : 'active',
            'archived_at' => $mount->archived_at?->toISOString(), 'archive_reason' => $mount->archive_reason?->value,
            'archive_description' => $mount->archive_description, 'manufacturer' => $mount->manufacturer, 'label' => $mount->label,
            'serial' => $mount->serial, 'height' => $mount->height, 'mount_type' => $mount->mount_type,
            'color_id' => $mount->color_id, 'color' => $mount->color?->only(['id', 'label']),
            'firearm_id' => $mount->firearm_id, 'firearm' => $mount->firearm?->only(['id', 'label', 'manufacturer']),
            'mounted_since' => $this->mountedSince($mount), 'location_id' => $mount->location_id,
            'location' => $mount->location?->only(['id', 'label', 'full_label']), 'purchase_date' => $mount->purchase_date?->toDateString(),
            'purchase_price' => $mount->purchase_price, 'purchase_store_id' => $mount->purchase_store_id,
            'primary_photo_url' => $mount->pictures->first(fn ($picture) => $picture->pivot->is_primary)?->getUrl('medium') ?? $mount->pictures->first()?->getUrl('medium'),
            'pictures_count' => $mount->pictures->count(), 'thumbnail_urls' => $mount->pictures->take(3)->map(fn ($picture) => $picture->getUrl('thumbnail'))->values()->all(),
            'created_at' => $mount->created_at->toISOString(), 'updated_at' => $mount->updated_at->toISOString(),
        ];
    }
}
