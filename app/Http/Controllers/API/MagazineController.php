<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMagazineRequest;
use App\Http\Requests\UpdateMagazineRequest;
use App\Models\Magazine;
use App\Models\Picture;
use App\Transformers\MagazineTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class MagazineController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Magazine::class);

        $magazines = QueryBuilder::for(Magazine::class)
            ->allowedFilters('label', 'manufacturer', 'model_name', 'status')
            ->allowedSorts('label', 'manufacturer', 'capacity')
            ->with(['calibers', 'firearms'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($magazines, MagazineTransformer::class)->respond();
    }

    /**
     * @param  StoreMagazineRequest  $request
     * @return JsonResponse
     */
    public function store(StoreMagazineRequest $request): JsonResponse
    {
        $this->authorize('create', Magazine::class);

        $magazine = Magazine::create([
            ...$request->safe()->except(['calibers', 'firearms']),
            'user_id' => Auth::id(),
        ]);

        $magazine->calibers()->sync($request->safe()->input('calibers', []));
        $magazine->firearms()->sync($request->safe()->input('firearms', []));

        $magazine->load(['calibers', 'firearms']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function show(Magazine $magazine): JsonResponse
    {
        $this->authorize('view', $magazine);

        $magazine->load(['calibers', 'firearms']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  UpdateMagazineRequest  $request
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function update(UpdateMagazineRequest $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        $magazine->update($request->safe()->except(['calibers', 'firearms']));

        $magazine->calibers()->sync($request->safe()->input('calibers', []));
        $magazine->firearms()->sync($request->safe()->input('firearms', []));

        $magazine->load(['calibers', 'firearms']);

        return fractal($magazine, MagazineTransformer::class)->respond();
    }

    /**
     * @param  Request  $request
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
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

    /**
     * @param  Magazine  $magazine
     * @return JsonResponse
     */
    public function destroy(Magazine $magazine): JsonResponse
    {
        $this->authorize('delete', $magazine);

        $magazine->delete();

        return response()->json(null, 204);
    }
}
