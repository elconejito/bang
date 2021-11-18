<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class InventoryTotalSummaryTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param $total
     *
     * @return array
     */
    public function transform($total): array
    {
        return $total;
    }
}
