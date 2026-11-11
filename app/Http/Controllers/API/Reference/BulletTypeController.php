<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulletTypeRequest;
use App\Http\Requests\UpdateBulletTypeRequest;
use App\Models\Reference\BulletType;
use App\Transformers\BulletTypeTransformer;
use Illuminate\Http\JsonResponse;

class BulletTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(BulletType::withCount('ammunition')->orderBy('label')->get(), BulletTypeTransformer::class)->respond();
    }

    public function store(StoreBulletTypeRequest $request): JsonResponse
    {
        $bulletType = BulletType::create($request->validated());

        return fractal()->item($bulletType, BulletTypeTransformer::class)->respond();
    }

    public function update(UpdateBulletTypeRequest $request, BulletType $bulletType): JsonResponse
    {
        $bulletType->update($request->validated());

        return fractal()->item($bulletType, BulletTypeTransformer::class)->respond();
    }

    public function destroy(BulletType $bulletType): JsonResponse
    {
        if ($bulletType->isInUse()) {
            return response()->json([
                'message' => 'This bullet type is in use and cannot be deleted. Reassign the loads that use it first.',
            ], 409);
        }

        $bulletType->delete();

        return response()->json(null, 204);
    }
}
