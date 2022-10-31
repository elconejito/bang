<?php

namespace App\Transformers;

use App\Models\Reference\AmmunitionCondition;
use League\Fractal\TransformerAbstract;

class AmmunitionConditionTransformer extends TransformerAbstract
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
     * @param AmmunitionCondition $ammunition_condition
     *
     * @return array
     */
    public function transform(AmmunitionCondition $ammunition_condition): array
    {
        return $ammunition_condition->transform();
    }
}
