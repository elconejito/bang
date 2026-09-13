<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\SyncAssetPurchase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuppressorRequest;
use App\Http\Requests\UpdateSuppressorRequest;
use App\Models\Suppressor;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\SuppressorTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SuppressorController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Suppressor::class);

        $suppressors = QueryBuilder::for(Suppressor::class)
            ->allowedFilters('manufacturer', 'label', AllowedFilter::exact('caliber_id'), AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))
            ->allowedSorts('manufacturer', 'label')
            ->with(['caliber', 'color', 'firearm', 'location', 'orderAsset.order.store'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($suppressors, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  StoreSuppressorRequest  $request
     * @return JsonResponse
     */
    public function store(StoreSuppressorRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', Suppressor::class);

        $suppressor = DB::transaction(function () use ($request, $syncAssetPurchase): Suppressor {
            $suppressor = Suppressor::create([
                ...$request->safe()->except(['order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            $syncAssetPurchase->execute($suppressor, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $suppressor;
        }, attempts: 3);

        $suppressor->load(['caliber', 'color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function show(Suppressor $suppressor): JsonResponse
    {
        $this->authorize('view', $suppressor);

        $suppressor->load(['caliber', 'color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  UpdateSuppressorRequest  $request
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function update(UpdateSuppressorRequest $request, Suppressor $suppressor, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $suppressor);

        if ($suppressor->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this suppressor before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }

        DB::transaction(function () use ($suppressor, $request, $syncAssetPurchase): void {
            $suppressor->update($request->safe()->except(['order_id', 'cost']));
            $syncAssetPurchase->execute($suppressor, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        $suppressor->load(['caliber', 'color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function destroy(Suppressor $suppressor, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $suppressor);

        $blockers = $deleteAsset->execute($suppressor);

        if ($blockers !== []) {
            return response()->json(['message' => 'This suppressor cannot be permanently deleted.', 'code' => 'suppressor_delete_blocked', 'blockers' => $blockers], 409);
        }

        return response()->json(null, 204);
    }
}
