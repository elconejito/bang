<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\JWT;

class RefreshJwt
{
    public function __construct(private readonly JWT $jwt) {}

    public function execute(Request $request): string
    {
        $token = $this->jwt->unsetToken()->setRequest($request)->getToken();

        if ($token === null) {
            throw new AuthenticationException;
        }

        $manager = $this->jwt->manager();

        try {
            $claims = $manager->getJWTProvider()->decode($token->get());
            $manager->getPayloadFactory()->emptyClaims();
            $manager->setRefreshFlow()->decode($token);
            $user = User::query()->find($claims['sub'] ?? null);

            if (! $this->identityMatches($user, $claims['auth_uuid'] ?? null)) {
                throw new AuthenticationException;
            }

            return $this->jwt->setToken($token)->refresh();
        } finally {
            $manager->setRefreshFlow(false);
            $this->jwt->unsetToken();
        }
    }

    private function identityMatches(?User $user, mixed $authUuid): bool
    {
        return $user !== null
            && is_string($authUuid)
            && $authUuid !== ''
            && hash_equals($user->auth_uuid, $authUuid);
    }
}
