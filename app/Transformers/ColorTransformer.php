<?php

namespace App\Transformers;

use App\Models\Reference\Color;
use League\Fractal\TransformerAbstract;

class ColorTransformer extends TransformerAbstract
{
    public function transform(Color $color): array
    {
        return [
            ...$color->only(['id', 'label', 'short_label']),
            'items_count' => ($color->firearms_count ?? $color->firearms()->count())
                + ($color->suppressors_count ?? $color->suppressors()->count())
                + ($color->optics_count ?? $color->optics()->count())
                + ($color->lights_count ?? $color->lights()->count())
                + ($color->misc_accessories_count ?? $color->miscAccessories()->count())
                + ($color->magazines_count ?? $color->magazines()->count()),
        ];
    }
}
