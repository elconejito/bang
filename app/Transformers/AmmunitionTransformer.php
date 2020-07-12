<?php

namespace App\Transformers;

use App\Models\Ammunition;
use League\Fractal\TransformerAbstract;

class AmmunitionTransformer extends TransformerAbstract
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
     * @param Ammunition $ammunition
     *
     * @return array
     */
    public function transform(Ammunition $ammunition)
    {
        return $ammunition->transform();
    }
}
