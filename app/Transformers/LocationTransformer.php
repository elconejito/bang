<?php

namespace App\Transformers;

use App\Models\Location;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    /**
     * @param  Location  $location
     * @return array{
     *   id: int,
     *   label: string,
     *   description: string|null,
     *   location_type_id: int|null,
     *   user_id: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Location $location): array
    {
        $location->loadMissing(['pictures']);

        $primaryPicture = $location->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $location->pictures->first();

        $thumbnails = $location->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $location->id,
            'label' => $location->label,
            'description' => $location->description,
            'location_type_id' => $location->location_type_id,
            'user_id' => $location->user_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $location->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $location->created_at->toISOString(),
            'updated_at' => $location->updated_at->toISOString(),
        ];
    }
}
