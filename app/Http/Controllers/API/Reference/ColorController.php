<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\Color;
use App\Transformers\ColorTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(): JsonResponse
    {
        $colors = Color::withCount(['firearms', 'suppressors', 'optics', 'lights', 'miscAccessories', 'magazines'])->orderBy('label')->get();

        return fractal($colors, ColorTransformer::class)->respond();
    }

    public function store(Request $request): JsonResponse
    {
        $color = Color::create([
            ...$request->validate([
                'label' => ['required', 'string', 'max:255'],
                'short_label' => ['required', 'string', 'max:20'],
            ]),
            'user_id' => auth()->id(),
        ]);

        return fractal()->item($color, ColorTransformer::class)->respond();
    }

    public function update(Request $request, Color $color): JsonResponse
    {
        $color->update($request->validate([
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'short_label' => ['sometimes', 'required', 'string', 'max:20'],
        ]));

        return fractal()->item($color, ColorTransformer::class)->respond();
    }

    public function destroy(Color $color): JsonResponse
    {
        if ($color->isInUse()) {
            return response()->json(['message' => 'This color is in use and cannot be deleted. Reassign the items that use it first.'], 409);
        }

        $color->delete();

        return response()->json(null, 204);
    }
}
