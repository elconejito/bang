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
        $stores = QueryBuilder::for(Store::class)
            ->allowedFilters(['label'])
            ->allowedSorts(['label'])
            ->get();

        return fractal($stores, StoreTransformer::class)->respond();
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $store = Store::create([...$request->all(), 'user_id' => Auth::id()]);

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    public function show(int $store_id): JsonResponse
    {
        return fractal()->item(Store::findOrFail($store_id), StoreTransformer::class)->respond();
    }

    public function update(Request $request, Store $store): JsonResponse
    {
        $store->update($request->only(['label', 'description']));

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    public function destroy(Store $store): JsonResponse
    {
        $store->delete();

        return response()->json(null, 204);
    }
}
