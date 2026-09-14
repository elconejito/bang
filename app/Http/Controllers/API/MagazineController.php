<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\SyncAssetPurchase;
use App\Actions\Magazines\ChangeMagazineState;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMagazineStateRequest;
use App\Http\Requests\StoreMagazineRequest;
use App\Http\Requests\UpdateMagazineRequest;
use App\Models\Magazine;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\MagazineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MagazineController extends Controller
{
    public function changeState(ChangeMagazineStateRequest $request, Magazine $magazine, ChangeMagazineState $action): JsonResponse
    {
        $magazine = $action->handle($magazine, $request->validated());

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Magazine::class);

        $magazines = QueryBuilder::for(Magazine::class)
            ->allowedFilters(
                'label',
                'manufacturer',
                'model_name',
                AllowedFilter::callback('status', function ($query, $value): void {
                    match ($value) {
                        'in_gun' => $query->whereNotNull('current_firearm_id'),
                        'loaded' => $query->whereNull('current_firearm_id')->where('loaded_rounds', '>', 0),
                        'empty' => $query->whereNull('current_firearm_id')->where('loaded_rounds', 0),
                        default => null,
                    };
                }),
                AllowedFilter::custom('lifecycle_status', new FiltersLifecycleStatus)->default('active'),
            )
            ->allowedSorts('label', 'manufacturer', 'capacity')
            ->with(['calibers', 'firearms', 'color', 'orderAsset.order.store'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($magazines, MagazineTransformer::class)->respond();
    }

    /**
     * @param  StoreMagazineRequest  $request
     * @return JsonResponse
     */
    public function store(StoreMagazineRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', Magazine::class);

        $magazine = DB::transaction(function () use ($request, $syncAssetPurchase): Magazine {
            $magazine = Magazine::create([
                ...$request->safe()->except(['calibers', 'firearms', 'order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            $magazine->calibers()->sync($request->safe()->input('calibers', []));
            $magazine->firearms()->sync($request->safe()->input('firearms', []));
            $syncAssetPurchase->execute($magazine, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $magazine;
        }, attempts: 3);

        $magazine->load(['calibers', 'firearms', 'color']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function show(Magazine $magazine): JsonResponse
    {
        $this->authorize('view', $magazine);

        $magazine->load(['calibers', 'firearms', 'color']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  UpdateMagazineRequest  $request
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function update(UpdateMagazineRequest $request, Magazine $magazine, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $magazine);

        DB::transaction(function () use ($magazine, $request, $syncAssetPurchase): void {
            $magazine->update($request->safe()->except(['calibers', 'firearms', 'order_id', 'cost']));
            if ($request->has('calibers')) {
                $magazine->calibers()->sync($request->safe()->input('calibers', []));
            }
            if ($request->has('firearms')) {
                $magazine->firearms()->sync($request->safe()->input('firearms', []));
            }
            $syncAssetPurchase->execute($magazine, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        $magazine->load(['calibers', 'firearms']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function destroy(Magazine $magazine, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $magazine);

        $blockers = $deleteAsset->execute($magazine);

        if ($blockers !== []) {
            return response()->json(['message' => 'This magazine cannot be permanently deleted.', 'code' => 'magazine_delete_blocked', 'blockers' => $blockers], 409);
        }

        return response()->json(null, 204);
    }
}
