<?php

namespace App\Http\Controllers\API;

use App\Models\Suppressor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuppressorEventController extends AccessoryEventControllerBase
{
    public function index(Suppressor $suppressor): JsonResponse
    {
        $this->authorize('view', $suppressor);

        return $this->listEvents($suppressor);
    }

    public function store(Request $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->createEvent($request, $suppressor);
    }
}
