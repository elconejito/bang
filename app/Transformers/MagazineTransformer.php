<?php

namespace App\Transformers;

use App\Models\Magazine;
use League\Fractal\TransformerAbstract;

class MagazineTransformer extends TransformerAbstract
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
     * @param Magazine $magazine
     *
     * @return array
     */
    public function transform(Magazine $magazine)
    {
        return $magazine->transform();
    }
}
