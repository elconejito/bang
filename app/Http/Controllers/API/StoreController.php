<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Models\Store;
use App\Transformers\StoreTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class StoreController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Store::class);

        $stores = QueryBuilder::for(Store::class)
            ->allowedFilters(['label'])
            ->allowedSorts(['label'])
            ->get();

        return fractal($stores, StoreTransformer::class)->respond();
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Store::class);

        $store = Store::create([...$request->validated(), 'user_id' => Auth::id()]);

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    public function show(Store $store): JsonResponse
    {
        $this->authorize('view', $store);

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    public function update(Request $request, Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        $store->update($request->only(['label', 'description']));

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    public function destroy(Store $store): JsonResponse
    {
        $this->authorize('delete', $store);

        $store->delete();

        return response()->json(null, 204);
    }
}
