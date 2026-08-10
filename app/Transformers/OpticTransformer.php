<?php

namespace App\Transformers;

use App\Models\Optic;
use App\Transformers\Concerns\ResolvesMountedSince;
use League\Fractal\TransformerAbstract;

class OpticTransformer extends TransformerAbstract
{
    use ResolvesMountedSince;

    /**
     * @param  Optic  $optic
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   model_number: string|null,
     *   serial: string|null,
     *   optic_type: string|null,
     *   battery_type: string|null,
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
    public function transform(Optic $optic): array
    {
        $optic->loadMissing(['color', 'firearm', 'location', 'purchaseStore', 'pictures']);

        $primaryPicture = $optic->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $optic->pictures->first();

        $thumbnails = $optic->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $optic->id,
            'type' => 'optic',
            'status' => $optic->isArchived() ? 'archived' : 'active',
            'archived_at' => $optic->archived_at?->toISOString(),
            'archive_reason' => $optic->archive_reason?->value,
            'archive_description' => $optic->archive_description,
            'manufacturer' => $optic->manufacturer,
            'label' => $optic->label,
            'model_number' => $optic->model_number,
            'serial' => $optic->serial,
            'color_id' => $optic->color_id,
            'color' => $optic->color?->only(['id', 'label']),
            'optic_type' => $optic->optic_type,
            'battery_type' => $optic->battery_type,
            'firearm_id' => $optic->firearm_id,
            'firearm' => $optic->firearm
                ? $optic->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'mounted_since' => $this->mountedSince($optic),
            'location_id' => $optic->location_id,
            'location' => $optic->location
                ? $optic->location->only(['id', 'label', 'full_label'])
                : null,
            'purchase_date' => $optic->purchase_date?->toDateString(),
            'purchase_price' => $optic->purchase_price,
            'purchase_store_id' => $optic->purchase_store_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $optic->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $optic->created_at->toISOString(),
            'updated_at' => $optic->updated_at->toISOString(),
        ];
    }
}
