<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\JWT;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;
use Symfony\Component\HttpFoundation\Response;

class EnsureJwtIdentityMatchesUser
{
    public function __construct(private readonly JWT $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');
        $user = $guard->user();
        $authUuid = $this->currentTokenClaims($request)['auth_uuid'] ?? null;

        if (
            $user === null
            || ! is_string($authUuid)
            || $authUuid === ''
            || ! hash_equals($user->auth_uuid, $authUuid)
        ) {
            throw new AuthenticationException;
        }

        return $next($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function currentTokenClaims(Request $request): array
    {
        $token = $request->bearerToken();

        if ($token === null) {
            throw new AuthenticationException;
        }

        try {
            return $this->jwt->manager()->getJWTProvider()->decode($token);
        } catch (JWTException) {
            throw new AuthenticationException;
        }
    }
}
