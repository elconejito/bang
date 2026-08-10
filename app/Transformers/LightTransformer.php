<?php

namespace App\Transformers;

use App\Models\Light;
use App\Transformers\Concerns\ResolvesMountedSince;
use League\Fractal\TransformerAbstract;

class LightTransformer extends TransformerAbstract
{
    use ResolvesMountedSince;

    /**
     * @param  Light  $light
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   model_number: string|null,
     *   serial: string|null,
     *   lumens: int|null,
     *   battery_type: string|null,
     *   laser: string|null,
     *   beam_pattern: string|null,
     *   firearm_id: int|null,
     *   firearm: array{id: int, label: string}|null,
     *   mounted_since: string|null,
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
        $light->loadMissing(['color', 'firearm', 'location', 'purchaseStore', 'pictures']);

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
            'status' => $light->isArchived() ? 'archived' : 'active',
            'archived_at' => $light->archived_at?->toISOString(),
            'archive_reason' => $light->archive_reason?->value,
            'archive_description' => $light->archive_description,
            'manufacturer' => $light->manufacturer,
            'label' => $light->label,
            'model_number' => $light->model_number,
            'serial' => $light->serial,
            'color_id' => $light->color_id,
            'color' => $light->color?->only(['id', 'label']),
            'lumens' => $light->lumens,
            'battery_type' => $light->battery_type,
            'laser' => $light->laser,
            'beam_pattern' => $light->beam_pattern,
            'firearm_id' => $light->firearm_id,
            'firearm' => $light->firearm
                ? $light->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'mounted_since' => $this->mountedSince($light),
            'location_id' => $light->location_id,
            'location' => $light->location
                ? $light->location->only(['id', 'label', 'full_label'])
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
