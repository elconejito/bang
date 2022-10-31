<?php

namespace App\Transformers;

use App\Models\Reference\ShellType;
use League\Fractal\TransformerAbstract;

class ShellTypeTransformer extends TransformerAbstract
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
     * @param ShellType $shell_type
     *
     * @return array
     */
    public function transform(ShellType $shell_type)
    {
        return $shell_type->transform();
    }
}
