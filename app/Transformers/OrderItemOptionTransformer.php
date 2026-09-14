<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class OrderItemOptionTransformer extends TransformerAbstract
{
    /**
     * @param  array{type: string, id: int, label: string, secondary_label: string|null, order_id: int|null}  $option
     * @return array{type: string, id: int, label: string, secondary_label: string|null, order_id: int|null}
     */
    public function transform(array $option): array
    {
        return $option;
    }
}
