<?php

namespace App\Transformers;

use App\Models\Magazine;
use League\Fractal\TransformerAbstract;

class MagazineTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * @param  Magazine  $magazine
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string|null,
     *   model_name: string|null,
     *   capacity: int,
     *   serial_number: string|null,
     *   id_marking: string|null,
     *   status: string,
     *   loaded_ammunition_id: int|null,
     *   loaded_ammunition: array{id: int, label: string, manufacturer: string}|null,
     *   calibers: array<int, array{id: int, label: string}>,
     *   firearms: array<int, array{id: int, label: string, manufacturer: string}>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Magazine $magazine): array
    {
        $magazine->loadMissing(['calibers', 'compatibleFirearms', 'pictures', 'loadedAmmunition', 'location', 'currentFirearm']);

        $primaryPicture = $magazine->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $magazine->pictures->first();

        $thumbnails = $magazine->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $magazine->id,
            'type' => 'magazine',
            'manufacturer' => $magazine->manufacturer,
            'label' => $magazine->label,
            'model_name' => $magazine->model_name,
            'capacity' => $magazine->capacity,
            'serial_number' => $magazine->serial_number,
            'id_marking' => $magazine->id_marking,
            'status' => $magazine->display_status,
            'display_status' => $magazine->display_status,
            'load_state' => $magazine->load_state,
            'loaded_rounds' => $magazine->loaded_rounds,
            'loaded_ammunition_id' => $magazine->loaded_ammunition_id,
            'loaded_ammunition' => $magazine->loadedAmmunition
                ? $magazine->loadedAmmunition->only(['id', 'label', 'manufacturer'])
                : null,
            'calibers' => $magazine->calibers->map(fn ($c) => $c->only(['id', 'label']))->values()->all(),
            'firearms' => $magazine->compatibleFirearms->map(fn ($f) => $f->only(['id', 'label', 'manufacturer']))->values()->all(),
            'location' => $magazine->location?->only(['id', 'label']),
            'current_firearm' => $magazine->currentFirearm?->only(['id', 'label', 'manufacturer']),
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $magazine->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $magazine->created_at->toISOString(),
            'updated_at' => $magazine->updated_at->toISOString(),
        ];
    }
}
