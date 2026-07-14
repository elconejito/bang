<?php

namespace App\Transformers;

use App\Models\Note;
use League\Fractal\TransformerAbstract;

class NoteTransformer extends TransformerAbstract
{
    /**
     * @return array{id: int, note: string, created_at: string, updated_at: string}
     */
    public function transform(Note $note): array
    {
        return [
            'id' => $note->id,
            'note' => $note->note,
            'created_at' => $note->created_at->toISOString(),
            'updated_at' => $note->updated_at->toISOString(),
        ];
    }
}
