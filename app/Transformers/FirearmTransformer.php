<?php

namespace App\Transformers;

use App\Models\Firearm;
use League\Fractal\TransformerAbstract;

class FirearmTransformer extends TransformerAbstract
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
     * @param Firearm $firearm
     *
     * @return array
     */
    public function transform(Firearm $firearm)
    {
        return $firearm->transform();
    }
}
