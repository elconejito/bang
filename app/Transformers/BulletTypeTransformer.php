<?php

namespace App\Transformers;

use App\Models\Reference\BulletType;
use League\Fractal\TransformerAbstract;

class BulletTypeTransformer extends TransformerAbstract
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
     * @param BulletType $bullet_type
     *
     * @return array
     */
    public function transform(BulletType $bullet_type)
    {
        return $bullet_type->transform();
    }
}
