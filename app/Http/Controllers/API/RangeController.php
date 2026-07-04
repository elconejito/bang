<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Range;
use App\Transformers\RangeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class RangeController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Range::class);

        $ranges = QueryBuilder::for(Range::class)
            ->allowedFilters('label')
            ->allowedSorts('label', 'created_at')
            ->defaultSort('label')
            ->get();

        return fractal($ranges, RangeTransformer::class)->respond();
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Range::class);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $range = Range::create([...$data, 'user_id' => Auth::id()]);

        return fractal()->item($range, RangeTransformer::class)->respond(201);
    }

    public function show(Range $range): JsonResponse
    {
        $this->authorize('view', $range);

        return fractal()->item($range, RangeTransformer::class)->respond();
    }

    public function update(Request $request, Range $range): JsonResponse
    {
        $this->authorize('update', $range);

        $data = $request->validate([
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $range->update($data);

        return fractal()->item($range, RangeTransformer::class)->respond();
    }

    public function destroy(Range $range): JsonResponse
    {
        $this->authorize('delete', $range);

        $range->delete();

        return response()->json(null, 204);
    }
}
