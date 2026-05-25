<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = auth('api')->attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->tokenResponse($token);
    }

    public function register(Request $request): JsonResponse
    {
        (new CreateNewUser)->create($request->only(['name', 'email', 'password', 'password_confirmation']));

        return response()->json(['message' => 'Registration successful.'], 201);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    public function me(): JsonResponse
    {
        return response()->json(['data' => auth('api')->user()]);
    }

    private function tokenResponse(string $token): JsonResponse
    {
        return response()->json([
            'authorisation' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }
}
