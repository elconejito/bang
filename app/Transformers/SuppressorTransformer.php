<?php

namespace App\Transformers;

use App\Models\AccessoryEvent;
use App\Models\Suppressor;
use App\Transformers\Concerns\ResolvesMountedSince;
use League\Fractal\TransformerAbstract;

class SuppressorTransformer extends TransformerAbstract
{
    use ResolvesMountedSince;

    /**
     * @param  Suppressor  $suppressor
     * @return array{
     *   id: int,
     *   type: string,
     *   manufacturer: string,
     *   label: string,
     *   serial: string|null,
     *   caliber_id: int|null,
     *   caliber: array{id: int, label: string}|null,
     *   is_nfa: bool,
     *   mount_type: string|null,
     *   length: float|null,
     *   weight: float|null,
     *   nfa_form_type: string|null,
     *   nfa_approved_date: string|null,
     *   nfa_trust: string|null,
     *   rounds_fired: int,
     *   last_cleaned_rounds: int|null,
     *   firearm_id: int|null,
     *   firearm: array{id: int, label: string}|null,
     *   mounted_since: string|null,
     *   location_id: int|null,
     *   location: array{id: int, label: string}|null,
     *   purchase_date: string|null,
     *   purchase_price: float|null,
     *   purchase_store_id: int|null,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Suppressor $suppressor): array
    {
        $suppressor->loadMissing(['caliber', 'firearm', 'location', 'purchaseStore', 'pictures']);

        $primaryPicture = $suppressor->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $suppressor->pictures->first();

        $thumbnails = $suppressor->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        return [
            'id' => $suppressor->id,
            'type' => 'suppressor',
            'status' => $suppressor->isArchived() ? 'archived' : 'active',
            'archived_at' => $suppressor->archived_at?->toISOString(),
            'archive_reason' => $suppressor->archive_reason?->value,
            'archive_description' => $suppressor->archive_description,
            'manufacturer' => $suppressor->manufacturer,
            'label' => $suppressor->label,
            'serial' => $suppressor->serial,
            'caliber_id' => $suppressor->caliber_id,
            'caliber' => $suppressor->caliber
                ? $suppressor->caliber->only(['id', 'label'])
                : null,
            'is_nfa' => $suppressor->is_nfa,
            'mount_type' => $suppressor->mount_type,
            'length' => $suppressor->length,
            'weight' => $suppressor->weight,
            'nfa_form_type' => $suppressor->nfa_form_type,
            'nfa_approved_date' => $suppressor->nfa_approved_date?->toDateString(),
            'nfa_trust' => $suppressor->nfa_trust,
            'rounds_fired' => $suppressor->totalRoundsFired(),
            'last_cleaned_rounds' => $this->lastCleanedRounds($suppressor),
            'firearm_id' => $suppressor->firearm_id,
            'firearm' => $suppressor->firearm
                ? $suppressor->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'mounted_since' => $this->mountedSince($suppressor),
            'location_id' => $suppressor->location_id,
            'location' => $suppressor->location
                ? $suppressor->location->only(['id', 'label'])
                : null,
            'purchase_date' => $suppressor->purchase_date?->toDateString(),
            'purchase_price' => $suppressor->purchase_price,
            'purchase_store_id' => $suppressor->purchase_store_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $suppressor->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'created_at' => $suppressor->created_at->toISOString(),
            'updated_at' => $suppressor->updated_at->toISOString(),
        ];
    }

    /**
     * Round count snapshotted onto the most recent cleaning event.
     */
    private function lastCleanedRounds(Suppressor $suppressor): ?int
    {
        $rounds = AccessoryEvent::where('accessoryable_type', $suppressor->getMorphClass())
            ->where('accessoryable_id', $suppressor->id)
            ->where('event_type', 'CLEAN')
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->value('rounds');

        return $rounds !== null ? (int) $rounds : null;
    }
}
