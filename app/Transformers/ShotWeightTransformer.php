<?php

namespace App\Transformers;

use App\Models\Reference\ShotWeight;
use League\Fractal\TransformerAbstract;

class ShotWeightTransformer extends TransformerAbstract
{
    /**
     * @return array{id: int, label: string}
     */
    public function transform(ShotWeight $shotWeight): array
    {
        return $shotWeight->only(['id', 'label']);
    }
}
