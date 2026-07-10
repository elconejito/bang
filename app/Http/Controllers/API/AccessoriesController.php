<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Light;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Queries\Magazines\MagazineGroupQuery;
use App\Transformers\LightTransformer;
use App\Transformers\MiscAccessoryTransformer;
use App\Transformers\OpticTransformer;
use App\Transformers\SuppressorTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessoriesController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request, MagazineGroupQuery $magazineGroups): JsonResponse
    {
        $this->authorize('viewAny', Suppressor::class);

        $suppressors = Suppressor::with(['caliber', 'firearm', 'location'])->get();
        $optics = Optic::with(['firearm', 'location'])->get();
        $lights = Light::with(['firearm', 'location'])->get();
        $misc = MiscAccessory::with(['firearm', 'location'])->get();
        $magazines = $magazineGroups->get($request->user());

        return response()->json([
            'data' => [
                'suppressors' => fractal($suppressors, SuppressorTransformer::class)->toArray()['data'],
                'optics' => fractal($optics, OpticTransformer::class)->toArray()['data'],
                'lights' => fractal($lights, LightTransformer::class)->toArray()['data'],
                'misc' => fractal($misc, MiscAccessoryTransformer::class)->toArray()['data'],
                'magazines' => $magazines->map(function (array $group): array {
                    $items = $group['magazines'];
                    $magazine = $items->first();

                    return [
                        'key' => $group['key']->encode(),
                        'manufacturer' => $magazine->manufacturer,
                        'model_name' => $magazine->model_name,
                        'capacity' => $magazine->capacity,
                        'calibers' => $magazine->calibers->map->only(['id', 'label'])->values()->all(),
                        'summary' => [
                            'total' => $items->count(),
                            'in_gun' => $items->whereNotNull('current_firearm_id')->count(),
                            'loaded' => $items->whereNull('current_firearm_id')->where('loaded_rounds', '>', 0)->count(),
                            'empty' => $items->whereNull('current_firearm_id')->where('loaded_rounds', 0)->count(),
                        ],
                    ];
                })->values()->all(),
            ],
        ]);
    }
}
