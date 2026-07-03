<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\Purpose;
use App\Transformers\PurposeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurposeController extends Controller
{
    public function index(): JsonResponse
    {
        $purposes = Purpose::withCount('bullets')->orderBy('label')->get();

        return fractal($purposes, PurposeTransformer::class)->respond();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string'],
        ]);

        $purpose = Purpose::create([...$validated, 'user_id' => auth()->id()]);

        return fractal()->item($purpose, PurposeTransformer::class)->respond();
    }

    public function update(Request $request, Purpose $purpose): JsonResponse
    {
        $validated = $request->validate([
            'label' => ['sometimes', 'required', 'string'],
        ]);

        $purpose->update($validated);

        return fractal()->item($purpose, PurposeTransformer::class)->respond();
    }

    public function destroy(Purpose $purpose): JsonResponse
    {
        if ($purpose->isInUse()) {
            return response()->json([
                'message' => 'This purpose is in use and cannot be deleted. Reassign the loads that use it first.',
            ], 409);
        }

        $purpose->delete();

        return response()->json(null, 204);
    }
}
