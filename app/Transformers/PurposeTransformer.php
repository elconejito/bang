<?php

namespace App\Transformers;

use App\Models\Reference\Purpose;
use League\Fractal\TransformerAbstract;

class PurposeTransformer extends TransformerAbstract
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
     * @param Purpose $purpose
     *
     * @return array
     */
    public function transform(Purpose $purpose)
    {
        return $purpose->transform();
    }
}
