<?php

namespace App\Transformers;

use App\Models\Reference\ShotMaterial;
use League\Fractal\TransformerAbstract;

class ShotMaterialTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array{id: int, label: string, loads_count: int}
     */
    public function transform(ShotMaterial $shotMaterial): array
    {
        return [
            ...$shotMaterial->only(['id', 'label']),
            'loads_count' => $shotMaterial->ammunition_count ?? $shotMaterial->ammunition()->count(),
        ];
    }
}
