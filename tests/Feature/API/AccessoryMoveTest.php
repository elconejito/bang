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

    public function test_moving_an_optic_between_firearms_logs_unmount_and_mount_events(): void
    {
        $losingFirearm = Firearm::factory()->recycle($this->user)->create();
        $receivingFirearm = Firearm::factory()->recycle($this->user)->create();
        $optic = Optic::factory()->recycle($this->user)->create(['firearm_id' => $losingFirearm->id]);

        $optic->update(['firearm_id' => $receivingFirearm->id]);

        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $optic->id,
            'event_type' => 'MOUNT',
            'firearm_id' => $receivingFirearm->id,
        ]);
        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $optic->id,
            'event_type' => 'UNMOUNT',
            'firearm_id' => $losingFirearm->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/optics/{$optic->id}")
            ->assertOk()
            ->assertJsonPath('data.mounted_since', now()->toDateString());

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$losingFirearm->id}/activity?filter[type]=MOUNT")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['title' => "Unmounted {$optic->label}"]);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$receivingFirearm->id}/activity?filter[type]=MOUNT")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', "Mounted {$optic->label}");

        $this->actingAs($this->user, 'api')
            ->getJson("/optics/{$optic->id}/events?filter[group]=mount")
            ->assertOk()
            ->assertJsonPath('data.0.type', 'MOUNT')
            ->assertJsonPath('data.0.title', "Mounted on {$receivingFirearm->label}")
            ->assertJsonPath('data.1.type', 'UNMOUNT')
            ->assertJsonPath('data.1.title', "Unmounted from {$losingFirearm->label}");
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
