<?php

namespace App\Http\Controllers\API;

use App\Models\MiscAccessory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MiscAccessoryEventController extends AccessoryEventControllerBase
{
    public function index(MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('view', $miscAccessory);

        return $this->listEvents($miscAccessory);
    }

    public function store(Request $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->createEvent($request, $miscAccessory);
    }
}
