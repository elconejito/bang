<?php

namespace App\Transformers;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Optic;
use League\Fractal\TransformerAbstract;

class FirearmTransformer extends TransformerAbstract
{
    /**
     * @return array{
     *   id: int,
     *   label: string|null,
     *   manufacturer: string,
     *   model: string|null,
     *   customizer: string|null,
     *   custom_package: string|null,
     *   serial: string|null,
     *   status: string,
     *   archived_at: string|null,
     *   archive_reason: string|null,
     *   archive_description: string|null,
     *   location_id: int|null,
     *   location: array{id: int, label: string}|null,
     *   purchase_date: string|null,
     *   purchase_price: string|null,
     *   purchase_store_id: int|null,
     *   purchase_store: array{id: int, label: string}|null,
     *   calibers: array<int, array{id: int, label: string}>,
     *   rounds_fired: int,
     *   mounted_accessories: array<int, array{id: int, type: string, label: string, subtitle: string, is_nfa: bool}>,
     *   compatible_magazines_count: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Firearm $firearm): array
    {
        $firearm->loadMissing(['calibers', 'location', 'purchaseStore', 'pictures', 'suppressors', 'optics', 'lights', 'miscAccessories', 'magazines', 'currentMagazines.loadedAmmunition']);

        $primaryPicture = $firearm->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $firearm->pictures->first();

        $thumbnails = $firearm->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()
            ->all();

        return [
            'id' => $firearm->id,
            'label' => $firearm->label,
            'manufacturer' => $firearm->manufacturer,
            'model' => $firearm->model,
            'customizer' => $firearm->customizer,
            'custom_package' => $firearm->custom_package,
            'serial' => $firearm->serial,
            'status' => $firearm->isArchived() ? 'archived' : 'active',
            'archived_at' => $firearm->archived_at?->toISOString(),
            'archive_reason' => $firearm->archive_reason?->value,
            'archive_description' => $firearm->archive_description,
            'location_id' => $firearm->location_id,
            'location' => $firearm->location
                ? ['id' => $firearm->location->id, 'label' => $firearm->location->label]
                : null,
            'purchase_date' => $firearm->purchase_date?->toDateString(),
            'purchase_price' => $firearm->purchase_price,
            'purchase_store_id' => $firearm->purchase_store_id,
            'purchase_store' => $firearm->purchaseStore
                ? ['id' => $firearm->purchaseStore->id, 'label' => $firearm->purchaseStore->label]
                : null,
            'calibers' => $firearm->calibers->map(fn ($c) => [
                'id' => $c->id,
                'label' => $c->label,
            ])->all(),
            'rounds_fired' => $firearm->totalRoundsFired(),
            'mounted_accessories' => collect([
                ...$firearm->suppressors->map(fn ($s) => [
                    'id' => $s->id,
                    'type' => 'Suppressor',
                    'label' => $s->label,
                    'subtitle' => 'Suppressor',
                    'is_nfa' => (bool) $s->is_nfa,
                ]),
                ...$firearm->optics->map(fn ($o) => [
                    'id' => $o->id,
                    'type' => 'Optic',
                    'label' => $o->label,
                    'subtitle' => $this->opticSubtitle($o),
                    'is_nfa' => false,
                ]),
                ...$firearm->lights->map(fn ($l) => [
                    'id' => $l->id,
                    'type' => 'Light',
                    'label' => $l->label,
                    'subtitle' => $this->lightSubtitle($l),
                    'is_nfa' => false,
                ]),
                ...$firearm->miscAccessories->map(fn ($m) => [
                    'id' => $m->id,
                    'type' => 'Misc',
                    'label' => $m->label,
                    'subtitle' => $m->sub_type ? ucfirst($m->sub_type) : 'Accessory',
                    'is_nfa' => false,
                ]),
            ])->values()->all(),
            'compatible_magazines_count' => $firearm->magazines->count(),
            'compatible_magazines_context' => ['compatible_firearm_id' => $firearm->id],
            'current_magazines' => $firearm->currentMagazines->map(fn ($magazine) => [
                'id' => $magazine->id,
                'label' => $magazine->label,
                'manufacturer' => $magazine->manufacturer,
                'model_name' => $magazine->model_name,
                'id_marking' => $magazine->id_marking,
                'loaded_rounds' => $magazine->loaded_rounds,
                'capacity' => $magazine->capacity,
                'loaded_ammunition' => $magazine->loadedAmmunition?->only(['id', 'label', 'manufacturer']),
            ])->values()->all(),
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $firearm->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $firearm->created_at->toISOString(),
            'updated_at' => $firearm->updated_at->toISOString(),
        ];
    }

    /**
     * Build a human-friendly descriptor for a mounted optic, e.g. "Red dot optic".
     */
    private function opticSubtitle(Optic $optic): string
    {
        if (! $optic->optic_type) {
            return 'Optic';
        }

        return ucfirst(str_replace('_', ' ', $optic->optic_type)).' optic';
    }

    /**
     * Build a human-friendly descriptor for a mounted light, e.g. "Weapon light · 500 lm".
     */
    private function lightSubtitle(Light $light): string
    {
        return $light->lumens
            ? 'Weapon light · '.number_format((int) $light->lumens).' lm'
            : 'Weapon light';
    }
}
