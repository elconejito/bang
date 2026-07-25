<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MountTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_mount_can_store_standard_or_custom_height_and_mount_type(): void
    {
        $this->actingAs($this->user, 'api')->postJson('/mounts', [
            'manufacturer' => 'Scalarworks', 'label' => 'LEAP/01', 'height' => '1.93"', 'mount_type' => 'picatinny',
        ])->assertOk()->assertJsonPath('data.height', '1.93"')->assertJsonPath('data.mount_type', 'picatinny');

        $this->actingAs($this->user, 'api')->postJson('/mounts', [
            'manufacturer' => 'ADM', 'label' => 'Custom', 'height' => '1.70"', 'mount_type' => 'mlok',
        ])->assertOk()->assertJsonPath('data.height', '1.70"');
    }

    public function test_mount_rejects_invalid_mount_type(): void
    {
        $this->actingAs($this->user, 'api')->postJson('/mounts', [
            'manufacturer' => 'ADM', 'label' => 'Recon', 'mount_type' => 'dovetail',
        ])->assertUnprocessable()->assertJsonValidationErrors(['mount_type']);
    }
}
