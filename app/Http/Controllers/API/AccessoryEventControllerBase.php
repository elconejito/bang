<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AccessoryEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class AccessoryEventControllerBase extends Controller
{
    protected function listEvents(Model $entity): JsonResponse
    {
        $events = AccessoryEvent::with('firearm')
            ->where('accessoryable_type', get_class($entity))
            ->where('accessoryable_id', $entity->id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $events->map(fn (AccessoryEvent $e) => $this->transformEvent($e))->values(),
        ]);
    }

    protected function createEvent(Request $request, Model $entity): JsonResponse
    {
        $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'event_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $event = AccessoryEvent::create([
            'user_id' => Auth::id(),
            'accessoryable_type' => get_class($entity),
            'accessoryable_id' => $entity->id,
            'event_type' => $request->input('event_type'),
            'event_date' => $request->input('event_date'),
            'description' => $request->input('description'),
        ]);

        $event->load('firearm');

        return response()->json(['data' => $this->transformEvent($event)], 201);
    }

    /**
     * @return array{id: int, event_type: string, event_date: string, firearm_id: int|null, firearm: array{id: int, label: string|null, manufacturer: string}|null, description: string|null, created_at: string}
     */
    protected function transformEvent(AccessoryEvent $event): array
    {
        return [
            'id' => $event->id,
            'event_type' => $event->event_type,
            'event_date' => $event->event_date->toDateString(),
            'firearm_id' => $event->firearm_id,
            'firearm' => $event->firearm
                ? $event->firearm->only(['id', 'label', 'manufacturer'])
                : null,
            'description' => $event->description,
            'created_at' => $event->created_at->toISOString(),
        ];
    }
}
