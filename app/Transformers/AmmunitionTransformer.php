<?php

namespace App\Transformers;

use App\Models\Ammunition;
use League\Fractal\TransformerAbstract;

class AmmunitionTransformer extends TransformerAbstract
{
    /**
     * @param  Ammunition  $ammunition
     * @return array{
     *   id: int,
     *   manufacturer: string,
     *   label: string,
     *   weight: int|null,
     *   on_hand: int,
     *   reorder_min: int|null,
     *   reorder_target: int|null,
     *   caliber_id: int,
     *   caliber: array{id: int, label: string}|null,
     *   purpose_id: int|null,
     *   purpose: array{id: int, label: string}|null,
     *   bullet_type_id: int|null,
     *   bullet_type: array{id: int, label: string}|null,
     *   ammunition_casing_id: int|null,
     *   ammunition_casing: array{id: int, label: string}|null,
     *   ammunition_condition_id: int|null,
     *   ammunition_condition: array{id: int, label: string}|null,
     *   primer_type_id: int|null,
     *   primer_type: array{id: int, label: string}|null,
     *   shell_length_id: int|null,
     *   shell_length: array{id: int, label: string}|null,
     *   shell_type_id: int|null,
     *   shell_type: array{id: int, label: string}|null,
     *   shot_material_id: int|null,
     *   shot_material: array{id: int, label: string}|null,
     *   used_by_firearms: array<int, array{id: int, label: string, manufacturer: string}>,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Ammunition $ammunition): array
    {
        $ammunition->loadMissing([
            'caliber.activeFirearms', 'purpose', 'bulletType', 'ammunitionCasing',
            'ammunitionCondition', 'primerType', 'shellLength', 'shellType', 'shotMaterial',
            'pictures',
        ]);

        $primaryPicture = $ammunition->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $ammunition->pictures->first();

        $thumbnails = $ammunition->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $ammunition->id,
            'manufacturer' => $ammunition->manufacturer,
            'label' => $ammunition->label,
            'weight' => $ammunition->weight,
            'on_hand' => $ammunition->getAttributes()['inventory'] ?? 0,
            'reorder_min' => $ammunition->reorder_min,
            'reorder_target' => $ammunition->reorder_target,
            'caliber_id' => $ammunition->caliber_id,
            'caliber' => $ammunition->caliber
                ? ['id' => $ammunition->caliber->id, 'label' => $ammunition->caliber->label]
                : null,
            'purpose_id' => $ammunition->purpose_id,
            'purpose' => $ammunition->purpose
                ? ['id' => $ammunition->purpose->id, 'label' => $ammunition->purpose->label]
                : null,
            'bullet_type_id' => $ammunition->bullet_type_id,
            'bullet_type' => $ammunition->bulletType
                ? ['id' => $ammunition->bulletType->id, 'label' => $ammunition->bulletType->label]
                : null,
            'ammunition_casing_id' => $ammunition->ammunition_casing_id,
            'ammunition_casing' => $ammunition->ammunitionCasing
                ? ['id' => $ammunition->ammunitionCasing->id, 'label' => $ammunition->ammunitionCasing->label]
                : null,
            'ammunition_condition_id' => $ammunition->ammunition_condition_id,
            'ammunition_condition' => $ammunition->ammunitionCondition
                ? ['id' => $ammunition->ammunitionCondition->id, 'label' => $ammunition->ammunitionCondition->label]
                : null,
            'primer_type_id' => $ammunition->primer_type_id,
            'primer_type' => $ammunition->primerType
                ? ['id' => $ammunition->primerType->id, 'label' => $ammunition->primerType->label]
                : null,
            'shell_length_id' => $ammunition->shell_length_id,
            'shell_length' => $ammunition->shellLength
                ? ['id' => $ammunition->shellLength->id, 'label' => $ammunition->shellLength->label]
                : null,
            'shell_type_id' => $ammunition->shell_type_id,
            'shell_type' => $ammunition->shellType
                ? ['id' => $ammunition->shellType->id, 'label' => $ammunition->shellType->label]
                : null,
            'shot_material_id' => $ammunition->shot_material_id,
            'shot_material' => $ammunition->shotMaterial
                ? ['id' => $ammunition->shotMaterial->id, 'label' => $ammunition->shotMaterial->label]
                : null,
            'used_by_firearms' => ($ammunition->caliber?->activeFirearms ?? collect())
                ->map(fn ($f) => ['id' => $f->id, 'label' => $f->label, 'manufacturer' => $f->manufacturer])
                ->values()->all(),
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $ammunition->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $ammunition->created_at->toISOString(),
            'updated_at' => $ammunition->updated_at->toISOString(),
        ];
    }
}
