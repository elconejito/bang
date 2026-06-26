<?php

namespace App\Http\Controllers\API;

use App\Models\SessionLine;
use App\Models\Suppressor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SuppressorEventController extends AccessoryEventControllerBase
{
    public function index(Request $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('view', $suppressor);

        return $this->listEvents($request, $suppressor);
    }

    public function store(Request $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->createEvent($request, $suppressor);
    }

    /**
     * RANGE entries from training sessions where rounds ran through the can,
     * grouped by session and carrying a running cumulative total.
     *
     * @return Collection<int, array{id: string, type: string, group: string, date: string, title: string, subtitle: string|null}>
     */
    protected function rangeEntries(Model $entity): Collection
    {
        $sessions = SessionLine::with(['trainingSession', 'firearm'])
            ->where('suppressor_id', $entity->id)
            ->where('add_suppressor_count', true)
            ->get()
            ->groupBy('training_session_id')
            ->map(function (Collection $lines, int $sessionId): array {
                $session = $lines->first()->trainingSession;

                return [
                    'session_id' => $sessionId,
                    'date' => $session->session_date->toDateString(),
                    'rounds' => (int) $lines->sum('rounds'),
                    'firearm' => $lines->first()->firearm?->label
                        ?? $lines->first()->firearm?->manufacturer,
                ];
            })
            ->sortBy('date')
            ->values();

        $runningTotal = 0;

        return $sessions->map(function (array $session) use (&$runningTotal): array {
            $runningTotal += $session['rounds'];

            return [
                'id' => 'range-'.$session['session_id'],
                'type' => 'RANGE',
                'group' => 'range',
                'date' => $session['date'],
                'title' => "+{$session['rounds']} rounds".($session['firearm'] ? " · on {$session['firearm']}" : ''),
                'subtitle' => 'Running total → '.number_format($runningTotal).' rounds',
            ];
        });
    }
}
