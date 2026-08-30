<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\JWT;
use Tests\TestCase;

class PreferencesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_an_authenticated_user_can_update_their_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $this->withToken(app(JWT::class)->fromUser($user))
            ->patchJson('/auth/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.email', 'updated@example.com')
            ->assertJsonPath('message', 'Your profile information has been updated.')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.auth_uuid');

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
    }

    public function test_profile_update_allows_the_user_to_keep_their_existing_email(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);

        $this->withToken(app(JWT::class)->fromUser($user))
            ->patchJson('/auth/profile', [
                'name' => 'Renamed Owner',
                'email' => 'owner@example.com',
            ])
            ->assertOk();

        $this->assertSame('Renamed Owner', $user->refresh()->name);
    }

    public function test_profile_update_rejects_an_email_that_belongs_to_another_user(): void
    {
        $user = User::factory()->create(['name' => 'Original Name', 'email' => 'owner@example.com']);
        User::factory()->create(['email' => 'taken@example.com']);

        $this->withToken(app(JWT::class)->fromUser($user))
            ->patchJson('/auth/profile', [
                'name' => 'Updated Name',
                'email' => 'taken@example.com',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertSame('Original Name', $user->refresh()->name);
        $this->assertSame('owner@example.com', $user->email);
    }

    public function test_an_authenticated_user_can_change_their_password_and_receive_a_replacement_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $oldToken = app(JWT::class)->fromUser($user);

        $response = $this->withToken($oldToken)
            ->putJson('/auth/password', [
                'current_password' => 'current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Your password has been updated.')
            ->assertJsonStructure(['data' => ['id', 'name', 'email'], 'authorisation' => ['access_token', 'token_type', 'expires_in']]);

        $user->refresh();
        $this->assertTrue(Hash::check('new-secure-password', $user->password));
        $this->withToken($oldToken)->getJson('/auth/me')->assertUnauthorized();
        $this->withToken($response->json('authorisation.access_token'))
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_password_update_requires_the_current_password_and_a_matching_confirmation(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $token = app(JWT::class)->fromUser($user);

        $this->withToken($token)
            ->putJson('/auth/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);

        $this->withToken($token)
            ->putJson('/auth/password', [
                'current_password' => 'current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'does-not-match',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertTrue(Hash::check('current-password', $user->refresh()->password));
    }

    public function test_preferences_endpoints_require_authentication(): void
    {
        $this->patchJson('/auth/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])->assertUnauthorized();

        $this->putJson('/auth/password', [
            'current_password' => 'current-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertUnauthorized();
    }
}
