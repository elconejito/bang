<?php

namespace App\Transformers;

use App\Models\Reference\AmmunitionCasing;
use League\Fractal\TransformerAbstract;

class AmmunitionCasingTransformer extends TransformerAbstract
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
     * @param AmmunitionCasing $ammunition_casing
     *
     * @return array
     */
    public function transform(AmmunitionCasing $ammunition_casing): array
    {
        return $ammunition_casing->transform();
    }
}
