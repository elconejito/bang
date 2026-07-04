<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cartridge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartridgeController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Cartridge::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Cartridge::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Cartridge $cartridge): JsonResponse
    {
        $this->authorize('view', $cartridge);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Cartridge $cartridge): JsonResponse
    {
        $this->authorize('update', $cartridge);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Cartridge $cartridge): JsonResponse
    {
        $this->authorize('delete', $cartridge);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
