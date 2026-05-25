<?php

namespace App\Transformers;

use App\Models\Magazine;
use League\Fractal\TransformerAbstract;

class MagazineTransformer extends TransformerAbstract
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
    public function transform(Magazine $magazine)
    {
        return $magazine->toArray();
    }
}
