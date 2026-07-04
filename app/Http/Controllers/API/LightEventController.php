<?php

namespace App\Http\Controllers\API;

use App\Models\Light;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LightEventController extends AccessoryEventControllerBase
{
    public function index(Request $request, Light $light): JsonResponse
    {
        $this->authorize('view', $light);

        return $this->listEvents($request, $light);
    }

    public function store(Request $request, Light $light): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->createEvent($request, $light);
    }
}
