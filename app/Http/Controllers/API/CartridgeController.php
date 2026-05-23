<?php

namespace App\Http\Controllers\API;

use App\Models\Cartridge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartridgeController extends Controller
{
    public function index(): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Cartridge $cartridge): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Cartridge $cartridge): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Cartridge $cartridge): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
