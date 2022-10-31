<?php

namespace App\Transformers;

use App\Models\Reference\CaliberType;
use League\Fractal\TransformerAbstract;

class CaliberTypeTransformer extends TransformerAbstract
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
     * @param CaliberType $caliber_type
     *
     * @return array
     */
    public function transform(CaliberType $caliber_type)
    {
        return $caliber_type->transform();
    }
}
