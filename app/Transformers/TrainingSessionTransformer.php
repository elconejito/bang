<?php

namespace App\Transformers;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\DB;
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
     *   targets: array<int, array{id: int, label: string|null, distance: float, group_size: float, thumbnail_url: string|null, medium_url: string|null}>,
     *   created_at: string,
     *   updated_at: string,
     * }
     */
    public function transform(TrainingSession $training): array
    {
        $training->loadMissing(['range', 'lines.firearm.calibers', 'lines.ammunition.bulletType', 'lines.suppressor', 'targets.picture']);

        $firearmsUsed = $training->lines
            ->groupBy('firearm_id')
            ->map(fn ($lines) => [
                'firearm' => $lines->first()->firearm?->only(['id', 'label', 'manufacturer', 'model']),
                'rounds' => $lines->sum('rounds'),
            ])
            ->values()
            ->all();

        $lineTransformer = new SessionLineTransformer;

        // Batch-load average cost per round for ammo used in this session
        $ammoIds = $training->lines->pluck('ammunition_id')->unique()->filter();
        $costPerRound = $ammoIds->isNotEmpty()
            ? DB::table('cms.inventories')
                ->select('ammunition_id', DB::raw('SUM(cost) / NULLIF(SUM(rounds), 0) as cost_per_round'))
                ->where('cost', '>', 0)
                ->where('rounds', '>', 0)
                ->whereIn('ammunition_id', $ammoIds)
                ->groupBy('ammunition_id')
                ->pluck('cost_per_round', 'ammunition_id')
                ->toArray()
            : [];

        $lines = $training->lines->map(function ($line) use ($lineTransformer, $costPerRound) {
            $data = $lineTransformer->transform($line);
            $cpr = (float) ($costPerRound[$line->ammunition_id] ?? 0);
            $data['estimated_cost'] = ($line->deduct_ammo && $cpr > 0) ? round($cpr * $line->rounds, 2) : null;

            return $data;
        })->values()->all();

        $ammoCost = round(array_sum(array_column(
            array_filter($lines, fn ($l) => $l['estimated_cost'] !== null),
            'estimated_cost',
        )), 2);

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
            'ammo_cost' => $ammoCost,
            'firearms_used' => $firearmsUsed,
            'lines' => $lines,
            'targets' => $training->targets->map(fn ($t) => [
                'id' => $t->id,
                'label' => $t->label,
                'distance' => (float) $t->distance,
                'group_size' => (float) $t->group_size,
                'thumbnail_url' => $t->picture?->getUrl('thumbnail'),
                'medium_url' => $t->picture?->getUrl('medium'),
            ])->values()->all(),
            'created_at' => $training->created_at->toISOString(),
            'updated_at' => $training->updated_at->toISOString(),
        ];
    }
}
