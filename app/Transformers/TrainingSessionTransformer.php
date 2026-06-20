<?php

namespace App\Transformers;

use App\Models\TrainingSession;
use League\Fractal\TransformerAbstract;

class TrainingSessionTransformer extends TransformerAbstract
{
    /**
     * @param  TrainingSession  $training
     * @return array{
     *   id: int,
     *   label: string,
     *   description: string|null,
     *   session_date: string,
     *   session_day_of_week: string,
     *   range_id: int|null,
     *   range: array{id: int, label: string}|null,
     *   total_rounds: int,
     *   firearms_count: int,
     *   target_count: int,
     *   has_suppressor: bool,
     *   firearms_used: array<int, array{firearm: array<string, mixed>|null, rounds: int}>,
     *   lines: array<int, array<string, mixed>>,
     *   targets: array<int, mixed>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(TrainingSession $training): array
    {
        $training->loadMissing(['range', 'lines.firearm', 'lines.ammunition', 'lines.suppressor', 'targets']);

        $firearmsUsed = $training->lines
            ->groupBy('firearm_id')
            ->map(fn ($lines) => [
                'firearm' => $lines->first()->firearm?->only(['id', 'label', 'manufacturer', 'model']),
                'rounds' => $lines->sum('rounds'),
            ])
            ->values()
            ->all();

        $lineTransformer = new SessionLineTransformer;

        return [
            'id' => $training->id,
            'label' => $training->label,
            'description' => $training->description,
            'session_date' => $training->session_date->toDateString(),
            'session_day_of_week' => $training->session_date->format('D'),
            'range_id' => $training->range_id,
            'range' => $training->range
                ? $training->range->only(['id', 'label'])
                : null,
            'total_rounds' => $training->lines->sum('rounds'),
            'firearms_count' => $training->lines->pluck('firearm_id')->unique()->count(),
            'target_count' => $training->targets->count(),
            'has_suppressor' => $training->lines->whereNotNull('suppressor_id')->isNotEmpty(),
            'firearms_used' => $firearmsUsed,
            'lines' => $training->lines->map(fn ($line) => $lineTransformer->transform($line))->values()->all(),
            'targets' => [],
            'created_at' => $training->created_at->toISOString(),
            'updated_at' => $training->updated_at->toISOString(),
        ];
    }
}
