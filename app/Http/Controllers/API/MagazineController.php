<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magazine;
use App\Models\Picture;
use App\Transformers\MagazineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class MagazineController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Magazine::class);

        $magazines = QueryBuilder::for(Magazine::class)
            ->allowedFilters('label', 'manufacturer')
            ->allowedSorts('label', 'manufacturer')
            ->with(['calibers', 'firearms'])
            ->get();

        return fractal($magazines, MagazineTransformer::class)->respond();
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Magazine::class);

        $magazine = Magazine::create([
            ...$request->only(['label', 'manufacturer', 'model_name', 'capacity', 'serial_number', 'id_marking']),
            'user_id' => Auth::id(),
        ]);
        $magazine->calibers()->sync($request->input('calibers', []));
        $magazine->firearms()->sync($request->input('firearms', []));

        return fractal()->item($magazine, MagazineTransformer::class)->respond();
    }

    public function show(Magazine $magazine): JsonResponse
    {
        $this->authorize('view', $magazine);

        return fractal()->item($magazine, MagazineTransformer::class)->respond();
    }

    public function update(Request $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        $magazine->update([
            ...$request->only(['label', 'manufacturer', 'model_name', 'capacity', 'serial_number', 'id_marking']),
            'user_id' => Auth::id(),
        ]);
        $magazine->calibers()->sync($request->input('calibers', []));

        return fractal()->item($magazine, MagazineTransformer::class)->respond();
    }

    public function addPhoto(Request $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        $path = $request->file->store('public/images');
        $filename = str_replace('public/images/', '', $path);
        $picture = Picture::create(['name' => $filename, 'filename' => $filename]);
        $picture->resize();
        $magazine->pictures()->attach($picture);

        return response()->json([
            'message' => 'Successfully added Picture to Magazine',
            'data' => ['path' => $path],
        ]);
    }

    public function destroy(Magazine $magazine): JsonResponse
    {
        $this->authorize('delete', $magazine);

        $magazine->delete();

        return response()->json(null, 204);
    }
}
