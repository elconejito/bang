<?php

namespace App\Transformers;

use App\Models\Inventory;
use League\Fractal\TransformerAbstract;

class InventoryTransformer extends TransformerAbstract
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
     * @param  Inventory  $inventory
     *
     * @return array
     */
    public function transform(Inventory $inventory): array
    {
        return $inventory->toArray();
    }
}
