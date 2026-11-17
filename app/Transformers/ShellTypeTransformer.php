<?php

namespace App\Transformers;

use App\Models\Reference\ShellType;
use League\Fractal\TransformerAbstract;

class ShellTypeTransformer extends TransformerAbstract
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
    public function transform(ShellType $shellType): array
    {
        return [
            ...$shellType->only(['id', 'label']),
            'loads_count' => $shellType->ammunition_count ?? $shellType->ammunition()->count(),
        ];
    }
}
