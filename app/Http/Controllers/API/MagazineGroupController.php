<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexMagazineGroupsRequest;
use App\Http\Requests\IndexMagazinesInGroupRequest;
use App\Models\Firearm;
use App\Models\Magazine;
use App\Queries\Magazines\MagazineGroupQuery;
use App\Queries\Magazines\MagazinesInGroupQuery;
use Illuminate\Http\JsonResponse;

class MagazineGroupController extends Controller
{
    public function index(IndexMagazineGroupsRequest $request, MagazineGroupQuery $query): JsonResponse
    {
        $this->authorize('viewAny', Magazine::class);
        $validated = $request->validated();
        $filters = $validated['filter'] ?? [];
        $firearm = $this->compatibleFirearm($request->user()->id, $filters['compatible_firearm_id'] ?? null);
        $groups = $query->get($request->user(), $firearm, $filters['search'] ?? null, $filters['caliber_id'] ?? null, $validated['sort'] ?? 'manufacturer');

        return response()->json([
            'data' => $groups->map(fn (array $group): array => $this->groupSummary($group))->all(),
            'meta' => ['groups' => $groups->count(), 'magazines' => $groups->sum(fn (array $group): int => $group['magazines']->count())],
        ]);
    }

    public function magazines(int $group, IndexMagazinesInGroupRequest $request, MagazineGroupQuery $groups, MagazinesInGroupQuery $query): JsonResponse
    {
        $this->authorize('viewAny', Magazine::class);
        $representative = Magazine::query()->findOrFail($group);
        $key = $groups->keyFor($representative);

        $validated = $request->validated();
        $parameters = [...($validated['filter'] ?? []), 'sort' => $validated['sort'] ?? 'id_marking', 'per_page' => $validated['per_page'] ?? 25];
        $paginator = $query->paginate($request->user(), $key, $parameters);
        abort_if($paginator->total() === 0 && ! $query->builder($request->user(), $key)->exists(), 404);
        $firearm = $this->compatibleFirearm($request->user()->id, $parameters['compatible_firearm_id'] ?? null);
        $groupId = (int) $query->builder($request->user(), $key)->min('id');

        return response()->json([
            'data' => collect($paginator->items())->map(fn (Magazine $magazine): array => $this->magazineRow($magazine))->all(),
            'group' => $this->groupIdentity($groupId, $paginator->items()[0] ?? $representative),
            'context' => ['compatible_firearm' => $firearm?->only(['id', 'label', 'manufacturer'])],
            'meta' => ['current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'per_page' => $paginator->perPage(), 'from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()],
            'links' => ['first' => $paginator->url(1), 'last' => $paginator->url($paginator->lastPage()), 'prev' => $paginator->previousPageUrl(), 'next' => $paginator->nextPageUrl()],
        ]);
    }

    private function compatibleFirearm(int $userId, mixed $id): ?Firearm
    {
        return $id ? Firearm::query()->withoutGlobalScopes()->where('user_id', $userId)->findOrFail((int) $id) : null;
    }

    private function groupSummary(array $group): array
    {
        $magazines = $group['magazines'];
        $first = $magazines->first();

        return [...$this->groupIdentity((int) $magazines->min('id'), $first),
            'summary' => ['total' => $magazines->count(), 'in_gun' => $magazines->whereNotNull('current_firearm_id')->count(), 'loaded' => $magazines->whereNull('current_firearm_id')->where('loaded_rounds', '>', 0)->count(), 'empty' => $magazines->whereNull('current_firearm_id')->where('loaded_rounds', 0)->count()],
            'locations' => $magazines->whereNotNull('location_id')->groupBy('location_id')->map(fn ($items): array => ['id' => $items->first()->location_id, 'label' => $items->first()->location->label, 'count' => $items->count()])->values()->all(),
        ];
    }

    private function groupIdentity(int $groupId, Magazine $magazine): array
    {
        return ['key' => $groupId, 'manufacturer' => $magazine->manufacturer, 'model_name' => $magazine->model_name, 'capacity' => $magazine->capacity, 'calibers' => $magazine->calibers->map->only(['id', 'label'])->values()->all()];
    }

    private function magazineRow(Magazine $magazine): array
    {
        return ['id' => $magazine->id, 'id_marking' => $magazine->id_marking, 'display_status' => $magazine->display_status, 'load_state' => $magazine->load_state, 'loaded_rounds' => $magazine->loaded_rounds, 'capacity' => $magazine->capacity, 'loaded_ammunition' => $magazine->loadedAmmunition?->only(['id', 'manufacturer', 'label']), 'current_firearm' => $magazine->currentFirearm?->only(['id', 'label', 'manufacturer']), 'location' => $magazine->location?->only(['id', 'label'])];
    }
}
