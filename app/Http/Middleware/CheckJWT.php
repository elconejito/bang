<?php

namespace App\Http\Middleware;

use App;
use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckJWT
{
    protected $userRepository;

    /**
     * CheckJWT constructor.
     *
     * @param Auth0UserRepository $userRepository
     */
    public function __construct(Auth0UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $auth0 = App::make('auth0');

        $accessToken = $request->bearerToken() ?? '';

        try {
            $tokenInfo = $auth0->decodeJWT($accessToken);

            $user = $this->userRepository->getUserByDecodedJWT($tokenInfo);

            if (!$user) {
                return response()->json(["message" => "Unauthorized user"], 401);
            }

            Auth::login(App\Models\User::whereId($user->id)->firstOrFail());

        } catch (InvalidTokenException $e) {
            return response()->json(["message" => $e->getMessage()], 401);
        } catch (CoreException $e) {
            return response()->json(["message" => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
