<?php

namespace App\Http\Controllers\API;

use App\Models\Optic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpticEventController extends AccessoryEventControllerBase
{
    public function index(Optic $optic): JsonResponse
    {
        $this->authorize('view', $optic);

        return $this->listEvents($optic);
    }

    public function store(Request $request, Optic $optic): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->createEvent($request, $optic);
    }
}
