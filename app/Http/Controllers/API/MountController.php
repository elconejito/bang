<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMountRequest;
use App\Http\Requests\UpdateMountRequest;
use App\Models\Mount;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\MountTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MountController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Mount::class);
        $mounts = QueryBuilder::for(Mount::class)->allowedFilters('manufacturer', 'label', 'mount_type', AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))->allowedSorts('manufacturer', 'label', 'height')->with(['color', 'firearm', 'location'])->defaultSort('manufacturer')->get();

        return fractal($mounts, MountTransformer::class)->respond();
    }

    public function store(StoreMountRequest $request): JsonResponse
    {
        $this->authorize('create', Mount::class);
        $mount = Mount::create([...$request->validated(), 'user_id' => Auth::id()]);

        return fractal($mount->load(['color', 'firearm', 'location', 'purchaseStore']), MountTransformer::class)->respond();
    }

    public function show(Mount $mount): JsonResponse
    {
        $this->authorize('view', $mount);

        return fractal($mount->load(['color', 'firearm', 'location', 'purchaseStore']), MountTransformer::class)->respond();
    }

    public function update(UpdateMountRequest $request, Mount $mount): JsonResponse
    {
        $this->authorize('update', $mount);
        if ($mount->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this mount before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }
        $mount->update($request->validated());

        return fractal($mount->load(['color', 'firearm', 'location', 'purchaseStore']), MountTransformer::class)->respond();
    }

    public function destroy(Mount $mount, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $mount);
        $blockers = $deleteAsset->execute($mount);

        return $blockers === [] ? response()->json(null, 204) : response()->json(['message' => 'This mount cannot be permanently deleted.', 'blockers' => $blockers], 409);
    }
}
