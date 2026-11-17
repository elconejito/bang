<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShotMaterialRequest;
use App\Http\Requests\UpdateShotMaterialRequest;
use App\Models\Reference\ShotMaterial;
use App\Transformers\ShotMaterialTransformer;
use Illuminate\Http\JsonResponse;

class ShotMaterialController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShotMaterial::withCount('ammunition')->orderBy('label')->get(), ShotMaterialTransformer::class)->respond();
    }

    public function store(StoreShotMaterialRequest $request): JsonResponse
    {
        $shotMaterial = ShotMaterial::create($request->validated());

        return fractal()->item($shotMaterial, ShotMaterialTransformer::class)->respond();
    }

    public function update(UpdateShotMaterialRequest $request, ShotMaterial $shotMaterial): JsonResponse
    {
        $shotMaterial->update($request->validated());

        return fractal()->item($shotMaterial, ShotMaterialTransformer::class)->respond();
    }

    public function destroy(ShotMaterial $shotMaterial): JsonResponse
    {
        if ($shotMaterial->isInUse()) {
            return response()->json([
                'message' => 'This shot material is in use and cannot be deleted. Reassign the loads that use it first.',
            ], 409);
        }

        $shotMaterial->delete();

        return response()->json(null, 204);
    }
}
