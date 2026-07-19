<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccessoryEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class AccessoryEventControllerBase extends Controller
{
    /**
     * Coarse filter groups an entry can belong to.
     */
    private const GROUPS = ['range', 'mount', 'maintenance', 'added', 'location', 'lifecycle'];

    /**
     * Map a stored event_type to its display badge, filter group, and label.
     *
     * @var array<string, array{type: string, group: string, label: string}>
     */
    private const EVENT_META = [
        'ADDED' => ['type' => 'ADDED', 'group' => 'added', 'label' => 'Added to inventory'],
        'MOUNT' => ['type' => 'MOUNT', 'group' => 'mount', 'label' => 'Mounted'],
        'UNMOUNT' => ['type' => 'UNMOUNT', 'group' => 'mount', 'label' => 'Unmounted'],
        'CLEAN' => ['type' => 'CLEAN', 'group' => 'maintenance', 'label' => 'Cleaned'],
        'REPAIR' => ['type' => 'REPAIR', 'group' => 'maintenance', 'label' => 'Repair / Service'],
        'BATTERY_REPLACE' => ['type' => 'BATTERY', 'group' => 'maintenance', 'label' => 'Battery replaced'],
        'LOAD' => ['type' => 'LOAD', 'group' => 'maintenance', 'label' => 'Loaded'],
        'UNLOAD' => ['type' => 'UNLOAD', 'group' => 'maintenance', 'label' => 'Unloaded'],
        'LOCATION_CHANGE' => ['type' => 'LOCATION', 'group' => 'location', 'label' => 'Location changed'],
        'ARCHIVED' => ['type' => 'ARCHIVED', 'group' => 'lifecycle', 'label' => 'Archived'],
        'UNARCHIVED' => ['type' => 'UNARCHIVED', 'group' => 'lifecycle', 'label' => 'Unarchived'],
    ];

    /**
     * Return the merged, paginated history feed for an accessory.
     *
     * Merges logged AccessoryEvents with optional RANGE entries (rounds fired),
     * then applies `filter[group]` (range/mount/maintenance), a `sort` of `-date`
     * (newest, default) or `date` (oldest), and `page`/`per_page` pagination —
     * mirroring the firearm activity and ammunition inventory feeds.
     */
    protected function listEvents(Request $request, Model $entity): JsonResponse
    {
        $rangeEntries = $this->rangeEntries($entity);
        $eventEntries = $this->eventEntries($entity);

        // Range entries sit before same-date events to match the design ordering.
        $entries = $rangeEntries->concat($eventEntries);

        // Header counts reflect the full, unfiltered feed.
        $rangeCount = $rangeEntries->count();
        $mountCount = $eventEntries->where('group', 'mount')->count();

        // Filter by coarse group.
        $group = strtolower((string) $request->input('filter.group'));
        if (in_array($group, self::GROUPS, true)) {
            $entries = $entries->where('group', $group);
        }

        // Sort by date — `-date` (newest, default) or `date` (oldest). Stable sort
        // keeps range-before-event ordering for entries sharing a date.
        $entries = str_starts_with((string) $request->input('sort', '-date'), '-')
            ? $entries->sortByDesc('date')
            : $entries->sortBy('date');
        $entries = $entries->values();

        $perPage = min(max((int) $request->input('per_page', 8), 1), 100);
        $page = max((int) $request->input('page', 1), 1);
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
                'mount_count' => $mountCount,
            ],
        ]);
    }

    /**
     * Build normalized entries from the accessory's logged events.
     *
     * @return Collection<int, array{id: string, type: string, group: string, date: string, title: string, subtitle: string|null}>
     */
    private function eventEntries(Model $entity): Collection
    {
        return AccessoryEvent::with('firearm')
            ->where('accessoryable_type', $entity->getMorphClass())
            ->where('accessoryable_id', $entity->id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (AccessoryEvent $event) => $this->transformEventEntry($event));
    }

    /**
     * @return array{id: string, type: string, group: string, date: string, title: string, subtitle: string|null}
     */
    private function transformEventEntry(AccessoryEvent $event): array
    {
        $meta = self::EVENT_META[$event->event_type] ?? [
            'type' => $event->event_type,
            'group' => 'maintenance',
            'label' => $event->event_type,
        ];

        return [
            'id' => "event-{$event->id}",
            'type' => $meta['type'],
            'group' => $meta['group'],
            'date' => $event->event_date->toDateString(),
            'title' => $this->eventTitle($event, $meta['label']),
            'subtitle' => $this->eventSubtitle($event),
        ];
    }

    private function eventTitle(AccessoryEvent $event, string $label): string
    {
        $firearmLabel = $event->firearm?->label ?? $event->firearm?->manufacturer;

        if ($event->event_type === 'MOUNT' && $firearmLabel) {
            return "Mounted on {$firearmLabel}";
        }

        if ($event->event_type === 'UNMOUNT' && $firearmLabel) {
            return "Unmounted from {$firearmLabel}";
        }

        return $label;
    }

    private function eventSubtitle(AccessoryEvent $event): ?string
    {
        $parts = array_filter([
            $event->metadata['reason'] ?? $event->metadata['previous_reason'] ?? null,
            $event->rounds !== null ? 'At '.number_format($event->rounds).' rounds' : null,
            $event->description,
        ]);

        return $parts ? implode(' · ', $parts) : null;
    }

    /**
     * Optional RANGE (rounds-fired) entries. Overridden by accessory types that
     * accrue rounds through training sessions (e.g. suppressors).
     *
     * @return Collection<int, array{id: string, type: string, group: string, date: string, title: string, subtitle: string|null}>
     */
    protected function rangeEntries(Model $entity): Collection
    {
        return collect();
    }

    protected function createEvent(Request $request, Model $entity): JsonResponse
    {
        if (method_exists($entity, 'isArchived') && $entity->isArchived()) {
            return response()->json([
                'message' => 'Unarchive this item before logging new activity.',
                'code' => 'archived_item_activity_blocked',
            ], 409);
        }

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'event_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $event = AccessoryEvent::create([
            'user_id' => Auth::id(),
            'accessoryable_type' => $entity->getMorphClass(),
            'accessoryable_id' => $entity->id,
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'description' => $validated['description'] ?? null,
            'rounds' => $this->snapshotRounds($entity, $validated['event_type']),
        ]);

        $event->load('firearm');

        return response()->json(['data' => $this->transformEventEntry($event)], 201);
    }

    /**
     * Snapshot the accessory's current round count onto maintenance events so the
     * timeline can show "At N rounds" (e.g. when a suppressor was last cleaned).
     */
    private function snapshotRounds(Model $entity, string $eventType): ?int
    {
        $maintenance = (self::EVENT_META[$eventType]['group'] ?? null) === 'maintenance';

        if ($maintenance && method_exists($entity, 'totalRoundsFired')) {
            return $entity->totalRoundsFired();
        }

        return null;
    }
}
