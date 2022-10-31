<?php

namespace App\Transformers;

use App\Models\Training;
use League\Fractal\TransformerAbstract;

class TrainingTransformer extends TransformerAbstract
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
     * @param  Training  $training
     *
     * @return array
     */
    public function transform(Training $training): array
    {
        return $training->toArray();
    }
}
