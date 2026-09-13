<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\SyncAssetPurchase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMiscAccessoryRequest;
use App\Http\Requests\UpdateMiscAccessoryRequest;
use App\Models\MiscAccessory;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\MiscAccessoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MiscAccessoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', MiscAccessory::class);

        $misc = QueryBuilder::for(MiscAccessory::class)
            ->allowedFilters('manufacturer', 'label', 'sub_type', AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))
            ->allowedSorts('manufacturer', 'label', 'sub_type')
            ->with(['color', 'firearm', 'location', 'orderAsset.order.store'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($misc, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  StoreMiscAccessoryRequest  $request
     * @return JsonResponse
     */
    public function store(StoreMiscAccessoryRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', MiscAccessory::class);

        $misc = DB::transaction(function () use ($request, $syncAssetPurchase): MiscAccessory {
            $misc = MiscAccessory::create([
                ...$request->safe()->except(['order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            $syncAssetPurchase->execute($misc, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $misc;
        }, attempts: 3);

        $misc->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($misc, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function show(MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('view', $miscAccessory);

        $miscAccessory->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($miscAccessory, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  UpdateMiscAccessoryRequest  $request
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function update(UpdateMiscAccessoryRequest $request, MiscAccessory $miscAccessory, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        if ($miscAccessory->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this accessory before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }

        DB::transaction(function () use ($miscAccessory, $request, $syncAssetPurchase): void {
            $miscAccessory->update($request->safe()->except(['order_id', 'cost']));
            $syncAssetPurchase->execute($miscAccessory, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        $miscAccessory->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($miscAccessory, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function destroy(MiscAccessory $miscAccessory, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $miscAccessory);

        $blockers = $deleteAsset->execute($miscAccessory);

        if ($blockers !== []) {
            return response()->json(['message' => 'This accessory cannot be permanently deleted.', 'code' => 'misc_accessory_delete_blocked', 'blockers' => $blockers], 409);
        }

        return response()->json(null, 204);
    }
}
