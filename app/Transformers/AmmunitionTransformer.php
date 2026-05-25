<?php

namespace App\Transformers;

use App\Models\Ammunition;
use League\Fractal\TransformerAbstract;

class AmmunitionTransformer extends TransformerAbstract
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
     */
    public function transform(Ammunition $ammunition): array
    {
        return $ammunition->toArray();
    }
}
