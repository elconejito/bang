<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
            ->assertJsonPath('data.email', $user->email);
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
}
