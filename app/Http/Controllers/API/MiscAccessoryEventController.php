<?php

namespace App\Http\Controllers\API;

use App\Models\MiscAccessory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MiscAccessoryEventController extends AccessoryEventControllerBase
{
    public function index(Request $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('view', $miscAccessory);

        return $this->listEvents($request, $miscAccessory);
    }

    public function store(Request $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->createEvent($request, $miscAccessory);
    }
}
