<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Optic;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccessoryMoveTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_moving_an_optic_logs_a_mount_event_and_exposes_mounted_since(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $optic = Optic::factory()->recycle($this->user)->create(['firearm_id' => null]);

        $optic->update(['firearm_id' => $firearm->id]);

        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $optic->id,
            'event_type' => 'MOUNT',
            'firearm_id' => $firearm->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/optics/{$optic->id}")
            ->assertOk()
            ->assertJsonPath('data.mounted_since', now()->toDateString());
    }

    public function test_moving_a_light_logs_a_mount_event_and_exposes_mounted_since(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $light = Light::factory()->recycle($this->user)->create(['firearm_id' => null]);

        $light->update(['firearm_id' => $firearm->id]);

        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $light->id,
            'event_type' => 'MOUNT',
            'firearm_id' => $firearm->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/lights/{$light->id}")
            ->assertOk()
            ->assertJsonPath('data.mounted_since', now()->toDateString());
    }
}
