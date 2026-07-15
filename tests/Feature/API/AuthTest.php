<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\JWT;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_returns_token_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure(['authorisation' => ['access_token', 'token_type', 'expires_in']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'user@example.com', 'password' => bcrypt('password')]);

        $this->postJson('/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])->assertUnprocessable();
    }

    public function test_login_validates_required_fields(): void
    {
        $this->postJson('/auth/login', [])->assertUnprocessable();
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonMissingPath('data.auth_uuid');
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/auth/me')->assertUnauthorized();
    }

    public function test_logout_succeeds_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Successfully logged out.');
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/auth/logout')->assertUnauthorized();
    }

    public function test_refresh_returns_a_new_token_for_the_same_user_identity(): void
    {
        $user = User::factory()->create();
        $token = app(JWT::class)->fromUser($user);

        $this->travel((int) config('jwt.ttl') + 1)->minutes();

        $response = $this->withToken($token)->postJson('/auth/refresh');

        $response
            ->assertOk()
            ->assertJsonStructure(['authorisation' => ['access_token', 'token_type', 'expires_in']]);

        $this->withToken($response->json('authorisation.access_token'))
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_access_and_refresh_reject_a_rotated_auth_uuid(): void
    {
        $user = User::factory()->create();
        $token = app(JWT::class)->fromUser($user);

        $user->forceFill(['auth_uuid' => (string) Str::uuid()])->save();

        $this->withToken($token)->getJson('/auth/me')->assertUnauthorized();
        $this->withToken($token)->postJson('/auth/refresh')->assertUnauthorized();
    }

    public function test_access_and_refresh_reject_a_recreated_user_with_the_same_id(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;
        $token = app(JWT::class)->fromUser($user);

        $user->delete();
        User::unguarded(fn (): User => User::factory()->create(['id' => $userId]));

        $this->withToken($token)->getJson('/auth/me')->assertUnauthorized();
        $this->withToken($token)->postJson('/auth/refresh')->assertUnauthorized();
    }

    public function test_access_and_refresh_reject_a_token_without_an_auth_uuid_claim(): void
    {
        $user = User::factory()->create();
        $token = $this->removeClaim(app(JWT::class)->fromUser($user), 'auth_uuid');

        $this->withToken($token)->getJson('/auth/me')->assertUnauthorized();
        $this->withToken($token)->postJson('/auth/refresh')->assertUnauthorized();
    }

    private function removeClaim(string $token, string $claim): string
    {
        $jwt = app(JWT::class);
        $claims = $jwt->setToken($token)->getPayload()->toArray();
        unset($claims[$claim]);

        $tokenWithoutClaim = $jwt->manager()->getJWTProvider()->encode($claims);
        $decodedClaims = $jwt->manager()->getJWTProvider()->decode($tokenWithoutClaim);
        $this->assertArrayNotHasKey($claim, $decodedClaims);
        $jwt->unsetToken();

        return $tokenWithoutClaim;
    }
}
