<?php

namespace App\Transformers;

use App\Models\Caliber;
use League\Fractal\TransformerAbstract;

class CaliberTransformer extends TransformerAbstract
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
     * A Fractal transformer.
     *
     *
     * @return array
     */
    public function transform(Caliber $caliber)
    {
        return array_merge($caliber->toArray(), [
            'firearms_count' => $caliber->firearms_count ?? $caliber->firearms()->count(),
            'loads_count' => $caliber->ammunition_count ?? $caliber->ammunition()->count(),
        ]);
    }
}
