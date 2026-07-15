<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPOpenSourceSaver\JWTAuth\JWT;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs(Authenticatable $user, $guard = null): static
    {
        if ($guard === 'api') {
            $this->forgetApiAuthentication();

            $token = auth('api')->tokenById($user->getAuthIdentifier());

            if ($token === null) {
                throw new \RuntimeException('Unable to issue an API token for the test user.');
            }

            return $this->withToken($token);
        }

        return parent::actingAs($user, $guard);
    }

    public function withToken($token, string $type = 'Bearer'): static
    {
        $this->forgetApiAuthentication();

        return parent::withToken($token, $type);
    }

    private function forgetApiAuthentication(): void
    {
        $this->app->make('auth')->forgetGuards();
        $this->app->make(JWT::class)->unsetToken();
    }
}
