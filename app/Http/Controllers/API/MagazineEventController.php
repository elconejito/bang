<?php

namespace App\Http\Controllers\API;

use App\Models\Magazine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagazineEventController extends AccessoryEventControllerBase
{
    public function index(Request $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('view', $magazine);

        return $this->listEvents($request, $magazine);
    }

    public function store(Request $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->createEvent($request, $magazine);
    }
}
