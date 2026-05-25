<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirearmRequest;
use App\Models\Firearm;
use App\Transformers\FirearmTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class FirearmController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Firearm::class);

        $firearms = QueryBuilder::for(Firearm::class)
            ->allowedFilters(['manufacturer', 'model', 'label'])
            ->allowedSorts(['manufacturer', 'model', 'label'])
            ->with(['calibers'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($firearms, FirearmTransformer::class)->respond();
    }

    public function store(StoreFirearmRequest $request): JsonResponse
    {
        $this->authorize('create', Firearm::class);

        $firearm = Firearm::create([
            ...$request->only(['manufacturer', 'model', 'label']),
            'user_id' => Auth::id(),
        ]);
        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function show(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        $firearm->load('calibers');

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function update(Request $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        $firearm->update([
            ...$request->only(['manufacturer', 'model', 'label']),
            'user_id' => Auth::id(),
        ]);
        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function destroy(Firearm $firearm): JsonResponse
    {
        $this->authorize('delete', $firearm);

        $firearm->delete();

        return response()->json(null, 204);
    }
}
