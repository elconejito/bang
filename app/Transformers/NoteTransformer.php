<?php

namespace App\Transformers;

use App\Models\Note;
use League\Fractal\TransformerAbstract;

class NoteTransformer extends TransformerAbstract
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
     * @param  Note  $note
     *
     * @return array
     */
    public function transform(Note $note): array
    {
        return $note->toArray();
    }
}
