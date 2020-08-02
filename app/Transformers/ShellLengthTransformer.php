<?php

namespace App\Transformers;

use App\Models\Reference\ShellLength;
use League\Fractal\TransformerAbstract;

class ShellLengthTransformer extends TransformerAbstract
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
     * @param ShellLength $shell_length
     *
     * @return array
     */
    public function transform(ShellLength $shell_length)
    {
        return $shell_length->transform();
    }
}
