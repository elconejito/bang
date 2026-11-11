<?php

namespace App\Transformers;

use App\Models\Reference\BulletType;
use League\Fractal\TransformerAbstract;

class BulletTypeTransformer extends TransformerAbstract
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
     * @return array{id: int, label: string, abbreviation: string, loads_count: int}
     */
    public function transform(BulletType $bulletType): array
    {
        return [
            ...$bulletType->only(['id', 'label', 'abbreviation']),
            'loads_count' => $bulletType->ammunition_count ?? $bulletType->ammunition()->count(),
        ];
    }
}
