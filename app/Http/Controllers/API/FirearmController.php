<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\SyncAssetPurchase;
use App\Actions\Firearms\DeleteFirearm;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirearmRequest;
use App\Http\Requests\UpdateFirearmRequest;
use App\Models\Firearm;
use App\Transformers\FirearmTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FirearmController extends Controller
{
    /**
     * Return a filtered, sorted list of the authenticated user's firearms.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Firearm::class);

        $firearms = QueryBuilder::for(Firearm::class)
            ->allowedFilters(
                'manufacturer',
                'model',
                'customizer',
                'custom_package',
                'label',
                AllowedFilter::exact('type'),
                AllowedFilter::callback('status', function ($query, mixed $value): void {
                    match (strtolower((string) $value)) {
                        'archived' => $query->whereNotNull('archived_at'),
                        'all' => null,
                        default => $query->whereNull('archived_at'),
                    };
                })->default('active'),
            )
            ->allowedSorts('manufacturer', 'model', 'customizer', 'custom_package', 'label', 'type')
            ->with([
                'calibers',
                'color',
                'location',
                'orderAsset.order.store',
                'orderAsset.order.store',
                'pictures',
                'suppressors',
                'optics',
                'lights',
                'miscAccessories',
                'mounts',
                'magazines',
                'currentMagazines.loadedAmmunition',
            ])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($firearms, FirearmTransformer::class)->respond();
    }

    /**
     * Create a new firearm for the authenticated user.
     *
     * @param  StoreFirearmRequest  $request
     * @return JsonResponse
     */
    public function store(StoreFirearmRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', Firearm::class);

        $firearm = DB::transaction(function () use ($request, $syncAssetPurchase): Firearm {
            $firearm = Firearm::create([
                ...$request->safe()->except(['calibers', 'order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            if ($request->has('calibers')) {
                $firearm->calibers()->sync($request->safe()->only(['calibers'])['calibers'] ?? []);
            }
            $syncAssetPurchase->execute($firearm, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $firearm;
        }, attempts: 3);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Return a single firearm with all relationships loaded.
     *
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function show(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        $firearm->load(['calibers', 'color', 'location', 'orderAsset.order.store', 'pictures']);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Update an existing firearm's attributes and caliber assignments.
     *
     * @param  UpdateFirearmRequest  $request
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function update(UpdateFirearmRequest $request, Firearm $firearm, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $firearm);

        DB::transaction(function () use ($firearm, $request, $syncAssetPurchase): void {
            $firearm->update($request->safe()->except(['calibers', 'order_id', 'cost']));
            if ($request->has('calibers')) {
                $firearm->calibers()->sync($request->safe()->only(['calibers'])['calibers'] ?? []);
            }
            $syncAssetPurchase->execute($firearm, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Delete a firearm.
     *
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function destroy(Firearm $firearm, DeleteFirearm $deleteFirearm): JsonResponse
    {
        $this->authorize('delete', $firearm);

        $blockers = $deleteFirearm->execute($firearm);

        if ($blockers !== []) {
            return response()->json([
                'message' => 'This firearm cannot be permanently deleted.',
                'code' => 'firearm_delete_blocked',
                'blockers' => $blockers,
            ], 409);
        }

        return response()->json(null, 204);
    }
}
