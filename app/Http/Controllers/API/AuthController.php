<?php

namespace App\Http\Controllers\API;

use App\Actions\Auth\RefreshJwt;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Pictures\GetPictureStorageStatus;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

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

    /**
     * Refresh an expired JWT and return a new token.
     *
     * The JWT guard's refresh flow accepts expired tokens within the refresh TTL window,
     * so this route intentionally sits outside the auth:api middleware.
     *
     * @return JsonResponse
     */
    public function refresh(Request $request, RefreshJwt $refreshJwt): JsonResponse
    {
        try {
            return $this->tokenResponse($refreshJwt->execute($request));
        } catch (AuthenticationException|JWTException) {
            return response()->json(['message' => 'Session has expired. Please log in again.'], 401);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    public function me(GetPictureStorageStatus $getPictureStorageStatus): JsonResponse
    {
        return response()->json([
            'data' => auth('api')->user(),
            'meta' => ['picture_storage' => $getPictureStorageStatus->execute()],
        ]);
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
