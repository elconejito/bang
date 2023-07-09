<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Models\Store;
use App\Repositories\Interfaces\StoreRepository;
use App\Transformers\StoreTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoreController extends Controller
{
    private StoreRepository $repository;

    public function __construct(StoreRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $stores = $this->repository->all();

        return fractal($stores, StoreTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreStoreRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        // Create the new Store
        $data = array_merge(
            $request->all(),
            [
                'user_id' => Auth::id(),
            ],
        );

        $store = $this->repository->create($data);

        return fractal()->item($store, StoreTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param $store_id
     *
     * @return JsonResponse
     */
    public function show($store_id): JsonResponse
    {
        $store = $this->repository->find($store_id);

        return fractal()->item($store, StoreTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Store $store
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Store $store)
    {
        // Update data
        $store->label = $request->label;

        // Save it
        $store->save();

        session()->flash('message', 'Store has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('StoreController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Store $store
     *
     * @return void
     */
    public function destroy(Store $store)
    {
        //
    }
}
