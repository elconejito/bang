<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccessoryEvent;
use App\Models\Firearm;
use App\Models\SessionLine;
use Illuminate\Http\JsonResponse;

class FirearmActivityController extends Controller
{
    /**
     * Return a merged, date-sorted activity feed for a single firearm.
     *
     * Includes RANGE entries derived from training session lines and MOUNT
     * entries from accessory events, most-recent first.
     *
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function index(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        $sessionLines = SessionLine::with(['trainingSession.location', 'ammunition', 'suppressor'])
            ->where('firearm_id', $firearm->id)
            ->get();

        $rangeEntries = $sessionLines->groupBy('training_session_id')
            ->map(function ($lines) {
                $session = $lines->first()->trainingSession;
                $totalRounds = $lines->sum('rounds');
                $ammoLabels = $lines->map(fn ($l) => $l->ammunition?->label)->filter()->unique()->implode(' · ');
                $hasSuppressor = $lines->whereNotNull('suppressor_id')->isNotEmpty();

                $subtitleParts = array_filter([
                    $session->location?->label,
                    $ammoLabels ?: null,
                    $hasSuppressor ? 'suppressed' : null,
                ]);

                return [
                    'type' => 'RANGE',
                    'date' => $session->session_date->toDateString(),
                    'title' => "{$totalRounds} rounds · {$session->label}",
                    'subtitle' => implode(' · ', $subtitleParts) ?: null,
                    'session_id' => $session->id,
                    'event_id' => null,
                ];
            })
            ->values();

        $mountEntries = AccessoryEvent::with('accessoryable')
            ->where('firearm_id', $firearm->id)
            ->get()
            ->map(function (AccessoryEvent $event) {
                $accessoryLabel = $event->accessoryable?->label ?? 'accessory';
                $verb = str_contains(strtolower((string) $event->event_type), 'unmount') ? 'Unmounted' : 'Mounted';

                return [
                    'type' => 'MOUNT',
                    'date' => $event->event_date->toDateString(),
                    'title' => "{$verb} {$accessoryLabel}",
                    'subtitle' => $event->description,
                    'session_id' => null,
                    'event_id' => $event->id,
                ];
            });

        $entries = $rangeEntries->concat($mountEntries)
            ->sortByDesc('date')
            ->values();

        $lastSessionDate = $rangeEntries->sortByDesc('date')->first()['date'] ?? null;

        return response()->json([
            'data' => $entries,
            'meta' => [
                'total' => $entries->count(),
                'range_count' => $rangeEntries->count(),
                'last_session_date' => $lastSessionDate,
            ],
        ]);
    }
}
