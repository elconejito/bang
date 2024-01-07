<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','refresh','logout']]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        Log::debug(__METHOD__.':'.__LINE__, [$token, $credentials]);
        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('api')->user();
        return response()->json([
            'status'        => 'success',
            'user'          => $user,
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ]);
    }

    public function register(RegisterRequest $request){
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);

        return response()->json([
            'status'        => 'success',
            'message'       => 'User created successfully',
            'user'          => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status'        => 'success',
            'user'          => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type'  => 'bearer',
            ],
        ]);
    }

}
