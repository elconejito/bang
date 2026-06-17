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
     *   calibers: array<int, array{id: int, label: string}>,
     *   firearms: array<int, array{id: int, label: string, manufacturer: string}>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Magazine $magazine): array
    {
        $magazine->loadMissing(['calibers', 'firearms']);

        return [
            'id' => $magazine->id,
            'type' => 'magazine',
            'manufacturer' => $magazine->manufacturer,
            'label' => $magazine->label,
            'model_name' => $magazine->model_name,
            'capacity' => $magazine->capacity,
            'serial_number' => $magazine->serial_number,
            'id_marking' => $magazine->id_marking,
            'status' => $magazine->status,
            'calibers' => $magazine->calibers->map(fn ($c) => $c->only(['id', 'label']))->values()->all(),
            'firearms' => $magazine->firearms->map(fn ($f) => $f->only(['id', 'label', 'manufacturer']))->values()->all(),
            'created_at' => $magazine->created_at->toISOString(),
            'updated_at' => $magazine->updated_at->toISOString(),
        ];
    }
}
