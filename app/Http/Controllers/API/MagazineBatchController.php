<?php

namespace App\Http\Controllers\API;

use App\Actions\Magazines\CreateMagazineBatch;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMagazineBatchRequest;
use App\Models\Magazine;
use App\Transformers\MagazineTransformer;
use Illuminate\Http\JsonResponse;

class MagazineBatchController extends Controller
{
    public function store(StoreMagazineBatchRequest $request, CreateMagazineBatch $action): JsonResponse
    {
        $this->authorize('create', Magazine::class);

        $magazines = $action->handle($request->user(), $request->validated());

        return fractal($magazines, MagazineTransformer::class)
            ->addMeta([
                'created' => $magazines->count(),
                'first_marking' => $magazines->first()?->id_marking,
                'last_marking' => $magazines->last()?->id_marking,
            ])
            ->respond(201);
    }
}
