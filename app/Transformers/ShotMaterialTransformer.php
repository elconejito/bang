<?php

namespace App\Transformers;

use App\Models\Reference\ShotMaterial;
use League\Fractal\TransformerAbstract;

class ShotMaterialTransformer extends TransformerAbstract
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
     * @param ShotMaterial $shot_material
     *
     * @return array
     */
    public function transform(ShotMaterial $shot_material)
    {
        return $shot_material->transform();
    }
}
