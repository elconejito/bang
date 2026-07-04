<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Transformers\LightTransformer;
use App\Transformers\MagazineTransformer;
use App\Transformers\MiscAccessoryTransformer;
use App\Transformers\OpticTransformer;
use App\Transformers\SuppressorTransformer;
use Illuminate\Http\JsonResponse;

class AccessoriesController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Suppressor::class);

        $suppressors = Suppressor::with(['caliber', 'firearm', 'location'])->get();
        $optics = Optic::with(['firearm', 'location'])->get();
        $lights = Light::with(['firearm', 'location'])->get();
        $misc = MiscAccessory::with(['firearm', 'location'])->get();
        $magazines = Magazine::with(['calibers', 'firearms'])->get();

        return response()->json([
            'data' => [
                'suppressors' => fractal($suppressors, SuppressorTransformer::class)->toArray()['data'],
                'optics' => fractal($optics, OpticTransformer::class)->toArray()['data'],
                'lights' => fractal($lights, LightTransformer::class)->toArray()['data'],
                'misc' => fractal($misc, MiscAccessoryTransformer::class)->toArray()['data'],
                'magazines' => fractal($magazines, MagazineTransformer::class)->toArray()['data'],
            ],
        ]);
    }
}
