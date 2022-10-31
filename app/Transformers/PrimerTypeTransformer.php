<?php

namespace App\Transformers;

use App\Models\Reference\PrimerType;
use League\Fractal\TransformerAbstract;

class PrimerTypeTransformer extends TransformerAbstract
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
     * @param PrimerType $primer_type
     *
     * @return array
     */
    public function transform(PrimerType $primer_type)
    {
        return $primer_type->transform();
    }
}
