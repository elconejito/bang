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
     *   type_label: string|null,
     *   user_id: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   contents: array{
     *     firearms: array<int, array{id: int, label: string, manufacturer: string}>,
     *     suppressors: array<int, array{id: int, label: string, manufacturer: string}>,
     *     optics: array<int, array{id: int, label: string, manufacturer: string}>,
     *     lights: array<int, array{id: int, label: string, manufacturer: string}>,
     *     misc_accessories: array<int, array{id: int, label: string, manufacturer: string}>,
     *   },
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Location $location): array
    {
        $location->loadMissing(['pictures', 'type', 'firearms', 'suppressors', 'optics', 'lights', 'miscAccessories', 'magazines']);

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
            'type_label' => $location->type?->label,
            'user_id' => $location->user_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $location->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'contents' => [
                'firearms' => $location->firearms->map(fn ($f) => ['id' => $f->id, 'label' => $f->label, 'manufacturer' => $f->manufacturer])->values()->all(),
                'suppressors' => $location->suppressors->map(fn ($s) => ['id' => $s->id, 'label' => $s->label, 'manufacturer' => $s->manufacturer])->values()->all(),
                'optics' => $location->optics->map(fn ($o) => ['id' => $o->id, 'label' => $o->label, 'manufacturer' => $o->manufacturer])->values()->all(),
                'lights' => $location->lights->map(fn ($l) => ['id' => $l->id, 'label' => $l->label, 'manufacturer' => $l->manufacturer])->values()->all(),
                'misc_accessories' => $location->miscAccessories->map(fn ($m) => ['id' => $m->id, 'label' => $m->label, 'manufacturer' => $m->manufacturer])->values()->all(),
                'magazines' => $location->magazines->map(fn ($magazine) => [
                    'id' => $magazine->id,
                    'label' => $magazine->label,
                    'manufacturer' => $magazine->manufacturer,
                    'model_name' => $magazine->model_name,
                    'id_marking' => $magazine->id_marking,
                    'loaded_rounds' => $magazine->loaded_rounds,
                    'capacity' => $magazine->capacity,
                ])->values()->all(),
            ],
            'created_at' => $location->created_at->toISOString(),
            'updated_at' => $location->updated_at->toISOString(),
        ];
    }
}
