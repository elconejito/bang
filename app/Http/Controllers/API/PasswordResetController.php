<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\ResetUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function store(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink($request->validated());

        return response()->json([
            'message' => 'If an account exists for that email address, a password reset link has been sent.',
        ]);
    }

    public function update(ResetPasswordRequest $request, ResetUserPassword $resetUserPassword): JsonResponse
    {
        $input = $request->validated();
        $status = Password::reset(
            $input,
            function (User $user, string $password) use ($input, $resetUserPassword): void {
                $resetUserPassword->reset($user, [
                    'password' => $password,
                    'password_confirmation' => $input['password_confirmation'],
                ]);
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['message' => 'Your password has been reset.']);
    }
}
