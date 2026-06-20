<?php

namespace App\Transformers;

use App\Models\Range;
use League\Fractal\TransformerAbstract;

class RangeTransformer extends TransformerAbstract
{
    /**
     * @param  Range  $range
     * @return array{
     *   id: int,
     *   label: string,
     *   description: string|null,
     *   address: string|null,
     *   user_id: int,
     *   primary_photo_url: string|null,
     *   pictures_count: int,
     *   thumbnail_urls: array<int, string>,
     *   sessions_count: int,
     *   sessions: array<int, array{id: int, label: string, session_date: string, rounds: int}>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(Range $range): array
    {
        $range->loadMissing(['pictures', 'sessions.lines']);

        $primaryPicture = $range->pictures->first(fn ($p) => $p->pivot->is_primary)
            ?? $range->pictures->first();

        $thumbnails = $range->pictures
            ->filter(fn ($p) => $p->id !== $primaryPicture?->id)
            ->take(3)
            ->map(fn ($p) => $p->getUrl('thumbnail'))
            ->values()->all();

        $sessions = $range->sessions->sortByDesc('session_date')->values();

        return [
            'id' => $range->id,
            'label' => $range->label,
            'description' => $range->description,
            'address' => $range->address,
            'user_id' => $range->user_id,
            'primary_photo_url' => $primaryPicture?->getUrl('medium'),
            'pictures_count' => $range->pictures->count(),
            'thumbnail_urls' => $thumbnails,
            'sessions_count' => $sessions->count(),
            'sessions' => $sessions->map(fn ($s) => [
                'id' => $s->id,
                'label' => $s->label,
                'session_date' => $s->session_date->toDateString(),
                'rounds' => $s->lines->sum('rounds'),
            ])->values()->all(),
            'created_at' => $range->created_at->toISOString(),
            'updated_at' => $range->updated_at->toISOString(),
        ];
    }
}
