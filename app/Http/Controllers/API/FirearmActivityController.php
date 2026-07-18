<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ActivityEvent;
use App\Models\Firearm;
use App\Models\SessionLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class FirearmActivityController extends Controller
{
    /**
     * Return a merged activity feed for a single firearm.
     *
     * Includes RANGE entries derived from training session lines and MOUNT
     * entries from accessory events. Supports a `filter[type]` (RANGE/MOUNT),
     * a `sort` of `-date` (newest, default) or `date` (oldest), and
     * `page`/`per_page` pagination, mirroring the ammunition inventory ledger.
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

        $mountEntries = ActivityEvent::with('subject')
            ->where('firearm_id', $firearm->id)
            ->whereIn('type', ['MOUNT', 'UNMOUNT'])
            ->get()
            ->map(function (ActivityEvent $event) {
                $accessoryLabel = $event->subject?->label ?? 'accessory';
                $verb = $event->type === 'UNMOUNT' ? 'Unmounted' : 'Mounted';

                return [
                    'type' => 'MOUNT',
                    'date' => $event->occurred_at->toDateString(),
                    'title' => "{$verb} {$accessoryLabel}",
                    'subtitle' => $event->description,
                    'session_id' => null,
                    'event_id' => $event->id,
                ];
            });

        $lifecycleEntries = ActivityEvent::query()
            ->where('subject_type', $firearm->getMorphClass())
            ->where('subject_id', $firearm->id)
            ->whereIn('type', ['ARCHIVED', 'UNARCHIVED'])
            ->get()
            ->map(function (ActivityEvent $event) {
                $reason = $event->metadata['reason'] ?? $event->metadata['previous_reason'] ?? null;

                return [
                    'type' => $event->type,
                    'date' => $event->occurred_at->toDateString(),
                    'title' => $event->type === 'ARCHIVED' ? 'Archived' : 'Unarchived',
                    'subtitle' => collect([$reason ? ucfirst(str_replace('_', ' ', $reason)) : null, $event->description])->filter()->implode(' · ') ?: null,
                    'session_id' => null,
                    'event_id' => $event->id,
                ];
            });

        $entries = $rangeEntries->concat($mountEntries)->concat($lifecycleEntries);

        // Header stats reflect the full, unfiltered feed.
        $lastSessionDate = $rangeEntries->sortByDesc('date')->first()['date'] ?? null;
        $rangeCount = $rangeEntries->count();

        // Filter by type.
        $type = strtoupper((string) Request::input('filter.type'));
        if (in_array($type, ['RANGE', 'MOUNT', 'ARCHIVED', 'UNARCHIVED'], true)) {
            $entries = $entries->where('type', $type);
        }

        // Sort by date — `-date` (newest, default) or `date` (oldest).
        $entries = str_starts_with((string) Request::input('sort', '-date'), '-')
            ? $entries->sortByDesc('date')
            : $entries->sortBy('date');
        $entries = $entries->values();

        // Paginate the merged collection.
        $perPage = min(max((int) Request::input('per_page', 10), 1), 100);
        $page = max((int) Request::input('page', 1), 1);
        $total = $entries->count();
        $items = $entries->forPage($page, $perPage)->values();

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) max(ceil($total / $perPage), 1),
                'range_count' => $rangeCount,
                'last_session_date' => $lastSessionDate,
            ],
        ]);
    }
}
