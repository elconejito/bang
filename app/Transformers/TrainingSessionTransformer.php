<?php

namespace App\Transformers;

use App\Models\Training;
use App\Models\TrainingSession;
use League\Fractal\TransformerAbstract;

class TrainingSessionTransformer extends TransformerAbstract
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
     * @param  TrainingSession  $training
     *
     * @return array
     */
    public function transform(TrainingSession $training): array
    {
        return $training->toArray();
    }
}
