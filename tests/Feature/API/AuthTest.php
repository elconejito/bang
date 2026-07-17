<?php

namespace Tests\Feature\API;

use App\Actions\Users\ProvisionDefaultReferenceData;
use App\Models\Caliber;
use App\Models\Reference\CaliberType;
use App\Models\Reference\Purpose;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\JWT;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_configuration_reports_registration_availability(): void
    {
        config()->set('app.registration_enabled', true);

        $this->getJson('/auth/configuration')
            ->assertOk()
            ->assertJsonPath('data.registration_enabled', true)
            ->assertJsonPath('data.password_reset_enabled', true);
    }

    public function test_registration_creates_an_account_when_enabled(): void
    {
        config()->set('app.registration_enabled', true);
        $this->createDefaultCaliberTypes();

        $this->postJson('/auth/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertCreated();

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertSame(
            ['.22LR', '12 Gauge', '5.56×45mm NATO', '9mm Luger'],
            Caliber::withoutGlobalScopes()->where('user_id', $user->id)->orderBy('caliber')->pluck('caliber')->all()
        );
        $this->assertSame(
            ['Home/Self Defense', 'Hunting', 'Match/Competition', 'Range/Training'],
            Purpose::withoutGlobalScopes()->where('user_id', $user->id)->orderBy('label')->pluck('label')->all()
        );
        $this->assertSame(
            [
                '.22LR' => 'Rimfire',
                '12 Gauge' => 'Shotgun',
                '5.56×45mm NATO' => 'Centerfire',
                '9mm Luger' => 'Centerfire',
            ],
            Caliber::withoutGlobalScopes()
                ->with('caliberType')
                ->where('user_id', $user->id)
                ->orderBy('caliber')
                ->get()
                ->mapWithKeys(fn (Caliber $caliber): array => [$caliber->caliber => $caliber->caliberType->label])
                ->all()
        );
    }

    public function test_initial_database_seed_uses_the_same_idempotent_user_defaults(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::query()->where('email', config('app.test_user_email'))->firstOrFail();
        $provisioner = app(ProvisionDefaultReferenceData::class);

        $provisioner->execute($user);
        $provisioner->execute($user);

        $this->assertSame(4, Caliber::withoutGlobalScopes()->where('user_id', $user->id)->count());
        $this->assertSame(4, Purpose::withoutGlobalScopes()->where('user_id', $user->id)->count());
    }

    public function test_registration_returns_not_found_when_disabled(): void
    {
        config()->set('app.registration_enabled', false);

        $this->postJson('/auth/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertNotFound();
    }

    public function test_forgot_password_sends_an_spa_reset_link_without_disclosing_accounts(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'user@example.com']);
        $expectedMessage = 'If an account exists for that email address, a password reset link has been sent.';

        $this->postJson('/auth/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', $expectedMessage);
        $this->postJson('/auth/forgot-password', ['email' => 'missing@example.com'])
            ->assertOk()
            ->assertJsonPath('message', $expectedMessage);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = $notification->toMail($user)->actionUrl;

            return str_starts_with($url, rtrim((string) config('app.url'), '/').'/auth/reset-password?')
                && str_contains($url, 'token='.urlencode($notification->token))
                && str_contains($url, 'email='.urlencode($user->email));
        });
    }

    public function test_password_can_be_reset_and_existing_tokens_are_invalidated(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        $accessToken = app(JWT::class)->fromUser($user);
        $resetToken = Password::createToken($user);
        $originalAuthUuid = $user->auth_uuid;

        $this->postJson('/auth/reset-password', [
            'token' => $resetToken,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Your password has been reset.');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secure-password', $user->password));
        $this->assertNotSame($originalAuthUuid, $user->auth_uuid);
        $this->withToken($accessToken)->getJson('/auth/me')->assertUnauthorized();
        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'new-secure-password',
        ])->assertOk();
    }

    public function test_password_reset_rejects_an_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->postJson('/auth/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertUnprocessable();
    }

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

    public function test_me_disables_local_picture_uploads_when_aws_is_not_configured(): void
    {
        config()->set('filesystems.disks.pictures', [
            'driver' => 'local',
            'key' => null,
            'secret' => null,
            'region' => null,
            'bucket' => null,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('meta.picture_storage.driver', 'local')
            ->assertJsonPath('meta.picture_storage.aws_configured', false)
            ->assertJsonPath('meta.picture_storage.uploads_enabled', false)
            ->assertJsonPath(
                'meta.picture_storage.notice',
                'AWS photo storage is not configured. Photo uploads are unavailable.'
            );
    }

    public function test_me_reports_s3_picture_storage_as_ready_without_exposing_credentials(): void
    {
        config()->set('filesystems.disks.pictures', [
            'driver' => 's3',
            'key' => 'access-key',
            'secret' => 'secret-key',
            'region' => 'us-east-1',
            'bucket' => 'bang-pictures',
        ]);
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('meta.picture_storage.driver', 's3')
            ->assertJsonPath('meta.picture_storage.aws_configured', true)
            ->assertJsonPath('meta.picture_storage.uploads_enabled', true)
            ->assertJsonPath('meta.picture_storage.notice', null)
            ->assertJsonMissing(['access-key', 'secret-key']);
    }

    public function test_me_disables_uploads_when_s3_picture_storage_is_incomplete(): void
    {
        config()->set('filesystems.disks.pictures', [
            'driver' => 's3',
            'key' => null,
            'secret' => null,
            'region' => 'us-east-1',
            'bucket' => 'bang-pictures',
        ]);
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson('/auth/me')
            ->assertOk()
            ->assertJsonPath('meta.picture_storage.aws_configured', false)
            ->assertJsonPath('meta.picture_storage.uploads_enabled', false)
            ->assertJsonPath(
                'meta.picture_storage.notice',
                'AWS photo storage is not configured. Photo uploads are unavailable.'
            );
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

    private function createDefaultCaliberTypes(): void
    {
        $owner = User::factory()->create();

        foreach (['Centerfire', 'Rimfire', 'Shotgun'] as $label) {
            CaliberType::create(['label' => $label, 'user_id' => $owner->id]);
        }
    }
}
