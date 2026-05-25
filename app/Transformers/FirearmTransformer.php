<?php

namespace App\Transformers;

use App\Models\Firearm;
use League\Fractal\TransformerAbstract;

class FirearmTransformer extends TransformerAbstract
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
     *
     *
     * @return array
     */
    public function transform(Firearm $firearm)
    {
        return $firearm->toArray();
    }
}
