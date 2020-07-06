<?php

namespace App\Transformers;

use App\Models\Caliber;
use League\Fractal\TransformerAbstract;

class CaliberTransformer extends TransformerAbstract
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
     * @param Caliber $caliber
     *
     * @return array
     */
    public function transform(Caliber $caliber)
    {
        return $caliber->transform();
    }
}
