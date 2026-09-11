<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PreferencesController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request, UpdateUserProfileInformation $updateUserProfileInformation): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');
        $updateUserProfileInformation->update($user, $request->validated());

        return response()->json([
            'data' => $user->fresh(),
            'message' => 'Your profile information has been updated.',
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request, UpdateUserPassword $updateUserPassword): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');
        $updateUserPassword->update($user, $request->validated());

        return response()->json([
            'data' => $user->fresh(),
            'message' => 'Your password has been updated.',
            'authorisation' => [
                'access_token' => auth('api')->login($user),
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }
}
