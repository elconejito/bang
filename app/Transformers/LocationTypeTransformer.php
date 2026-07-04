<?php

namespace App\Transformers;

use App\Models\Reference\LocationType;
use League\Fractal\TransformerAbstract;

class LocationTypeTransformer extends TransformerAbstract
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
     * @param  LocationType  $location
     *
     * @return array
     */
    public function transform(LocationType $location): array
    {
        return $location->toArray();
    }
}
