<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\SyncAssetPurchase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMountRequest;
use App\Http\Requests\UpdateMountRequest;
use App\Models\Mount;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\MountTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MountController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Mount::class);
        $mounts = QueryBuilder::for(Mount::class)->allowedFilters('manufacturer', 'label', 'mount_type', AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))->allowedSorts('manufacturer', 'label', 'height')->with(['color', 'firearm', 'location', 'orderAsset.order.store'])->defaultSort('manufacturer')->get();

        return fractal($mounts, MountTransformer::class)->respond();
    }

    public function store(StoreMountRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', Mount::class);
        $mount = DB::transaction(function () use ($request, $syncAssetPurchase): Mount {
            $mount = Mount::create([
                ...$request->safe()->except(['order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            $syncAssetPurchase->execute($mount, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $mount;
        }, attempts: 3);

        return fractal($mount->load(['color', 'firearm', 'location', 'orderAsset.order.store']), MountTransformer::class)->respond();
    }

    public function show(Mount $mount): JsonResponse
    {
        $this->authorize('view', $mount);

        return fractal($mount->load(['color', 'firearm', 'location', 'orderAsset.order.store']), MountTransformer::class)->respond();
    }

    public function update(UpdateMountRequest $request, Mount $mount, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $mount);
        if ($mount->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this mount before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }
        DB::transaction(function () use ($mount, $request, $syncAssetPurchase): void {
            $mount->update($request->safe()->except(['order_id', 'cost']));
            $syncAssetPurchase->execute($mount, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        return fractal($mount->load(['color', 'firearm', 'location', 'orderAsset.order.store']), MountTransformer::class)->respond();
    }

    public function destroy(Mount $mount, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $mount);
        $blockers = $deleteAsset->execute($mount);

        return $blockers === [] ? response()->json(null, 204) : response()->json(['message' => 'This mount cannot be permanently deleted.', 'blockers' => $blockers], 409);
    }
}
