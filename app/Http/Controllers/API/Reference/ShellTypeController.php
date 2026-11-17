<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShellTypeRequest;
use App\Http\Requests\UpdateShellTypeRequest;
use App\Models\Reference\ShellType;
use App\Transformers\ShellTypeTransformer;
use Illuminate\Http\JsonResponse;

class ShellTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShellType::withCount('ammunition')->orderBy('label')->get(), ShellTypeTransformer::class)->respond();
    }

    public function store(StoreShellTypeRequest $request): JsonResponse
    {
        $shellType = ShellType::create($request->validated());

        return fractal()->item($shellType, ShellTypeTransformer::class)->respond();
    }

    public function update(UpdateShellTypeRequest $request, ShellType $shellType): JsonResponse
    {
        $shellType->update($request->validated());

        return fractal()->item($shellType, ShellTypeTransformer::class)->respond();
    }

    public function destroy(ShellType $shellType): JsonResponse
    {
        if ($shellType->isInUse()) {
            return response()->json([
                'message' => 'This shell type is in use and cannot be deleted. Reassign the loads that use it first.',
            ], 409);
        }

        $shellType->delete();

        return response()->json(null, 204);
    }
}
