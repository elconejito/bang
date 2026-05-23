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
        $firearm = Firearm::create([
            ...$request->only(['manufacturer', 'model', 'label']),
            'user_id' => Auth::id(),
        ]);
        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function show(int $firearm_id): JsonResponse
    {
        $firearm = Firearm::with(['calibers'])->findOrFail($firearm_id);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function update(Request $request, int $firearm_id): JsonResponse
    {
        $firearm = Firearm::findOrFail($firearm_id);
        $firearm->update([
            ...$request->only(['manufacturer', 'model', 'label']),
            'user_id' => Auth::id(),
        ]);
        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function destroy(int $firearm_id): JsonResponse
    {
        Firearm::findOrFail($firearm_id)->delete();

        return response()->json(null, 204);
    }
}
