<?php

namespace Tests\Feature;

use App\Models\Picture;
use App\Models\User;
use Database\Seeders\FullInventorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LogicException;
use Tests\TestCase;

class SeedDemoTest extends TestCase
{
    public function test_production_blocks_all_modes_and_direct_fixture_seeders_before_database_access(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        DB::shouldReceive('connection')->never();

        foreach (['user', 'empty', 'full'] as $mode) {
            $this->artisan('demo:seed', ['mode' => $mode])->assertFailed();
        }
        (new UserSeeder)->run();
        $this->expectException(LogicException::class);
        (new FullInventorySeeder)->run();
    }

    public function test_invalid_mode_and_missing_credentials_fail_before_reset(): void
    {
        DB::shouldReceive('connection')->never();
        $this->artisan('demo:seed', ['mode' => 'typo'])->assertExitCode(2);
        config(['app.test_user_password' => null]);
        $this->artisan('demo:seed', ['mode' => 'empty'])->assertFailed();
    }

    public function test_demo_picture_route_serves_the_signed_local_file_without_cloud_credentials(): void
    {
        config(['filesystems.disks.demo-pictures.root' => storage_path('framework/testing/disks/demo-pictures'),
            'filesystems.disks.pictures.key' => null]);
        $picture = Picture::factory()->make(['disk' => 'demo-pictures', 'user_id' => 1]);
        $disk = Storage::disk($picture->disk);
        try {
            $disk->put($picture->variantKey('thumbnail'), 'fixture contents');
            $response = $this->get($picture->getUrl());
            $response->assertOk();
            $this->assertSame('fixture contents', $response->streamedContent());
        } finally {
            $disk->deleteDirectory($picture->key_prefix);
        }
    }

    public function test_cycle_preserves_the_user_in_place_and_rebuilds_both_inventory_states(): void
    {
        config(['app.test_user_name' => 'Demo', 'app.test_user_email' => 'demo@example.test', 'app.test_user_password' => 'local-demo']);
        Storage::fake('demo-pictures');
        $this->artisan('demo:seed empty')->assertSuccessful();
        $this->assertDatabaseCount('cms.firearms', 0);
        $this->assertDatabaseCount('cms.calibers', 16);
        $this->beforeApplicationDestroyed(fn () => $this->artisan('migrate:fresh')->assertSuccessful());

        foreach ([1, 2] as $cycle) {
            $this->artisan('demo:seed full')->assertSuccessful();
            $user = User::sole();
            $this->artisan('demo:seed user')->assertSuccessful();
            $this->assertSame($user->auth_uuid, User::sole()->auth_uuid);
            $this->assertDatabaseCount('cms.magazines', 28);
            $this->assertDatabaseCount('cms.session_lines', 16);
            $this->assertDatabaseCount('cms.order_assets', 7);
            $this->assertDatabaseCount('cms.targets', 1);
            $this->assertDatabaseHas('cms.ammunition', ['inventory' => 180]);
            $this->assertDatabaseHas('cms.ammunition', ['inventory' => 0]);
            $picture = Picture::withoutGlobalScopes()->firstOrFail();
            foreach (array_keys(Picture::VARIANTS) as $variant) {
                Storage::disk('demo-pictures')->assertExists($picture->variantKey($variant));
            }

            $this->actingAs($user, 'api')->getJson('/dashboard')->assertOk()
                ->assertJsonPath('data.stats.sessions_12mo', 16)
                ->assertJsonPath('data.stats.rounds_on_hand', 190);

            $this->artisan('demo:seed empty')->assertSuccessful();
            $this->assertDatabaseCount('idam.users', 1);
            $this->assertDatabaseCount('cms.firearms', 0);
            $this->assertDatabaseCount('cms.orders', 0);
            $this->assertDatabaseCount('cms.pictures', 0);
            Storage::disk('demo-pictures')->assertMissing($picture->variantKey('thumbnail'));
        }
    }
}
