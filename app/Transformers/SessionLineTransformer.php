<?php

namespace App\Transformers;

use App\Models\SessionLine;
use League\Fractal\TransformerAbstract;

class SessionLineTransformer extends TransformerAbstract
{
    /**
     * @param  SessionLine  $line
     * @return array{
     *   id: int,
     *   training_session_id: int,
     *   firearm_id: int,
     *   firearm: array{id: int, label: string|null, manufacturer: string, model: string|null}|null,
     *   ammunition_id: int,
     *   ammunition: array{id: int, label: string, manufacturer: string}|null,
     *   suppressor_id: int|null,
     *   suppressor: array{id: int, label: string, is_nfa: bool}|null,
     *   rounds: int,
     *   deduct_ammo: bool,
     *   add_firearm_count: bool,
     *   add_suppressor_count: bool,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(SessionLine $line): array
    {
        $line->loadMissing(['firearm', 'ammunition', 'suppressor']);

        return [
            'id' => $line->id,
            'training_session_id' => $line->training_session_id,
            'firearm_id' => $line->firearm_id,
            'firearm' => $line->firearm
                ? $line->firearm->only(['id', 'label', 'manufacturer', 'model'])
                : null,
            'ammunition_id' => $line->ammunition_id,
            'ammunition' => $line->ammunition
                ? $line->ammunition->only(['id', 'label', 'manufacturer'])
                : null,
            'suppressor_id' => $line->suppressor_id,
            'suppressor' => $line->suppressor
                ? ['id' => $line->suppressor->id, 'label' => $line->suppressor->label, 'is_nfa' => $line->suppressor->is_nfa]
                : null,
            'rounds' => $line->rounds,
            'deduct_ammo' => $line->deduct_ammo,
            'add_firearm_count' => $line->add_firearm_count,
            'add_suppressor_count' => $line->add_suppressor_count,
            'created_at' => $line->created_at->toISOString(),
            'updated_at' => $line->updated_at->toISOString(),
        ];
    }
}
