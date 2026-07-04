<?php

namespace Tests\Feature\API;

use App\Models\AccessoryEvent;
use App\Models\Firearm;
use App\Models\SessionLine;
use App\Models\Suppressor;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SuppressorTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_moving_between_firearms_logs_a_mount_event(): void
    {
        $from = Firearm::factory()->recycle($this->user)->create(['label' => 'Range Toy']);
        $to = Firearm::factory()->recycle($this->user)->create(['label' => 'Nightstand']);

        $suppressor = Suppressor::factory()->recycle($this->user)->create(['firearm_id' => $from->id]);

        // Direct A -> B move (the previously unhandled gap).
        $suppressor->update(['firearm_id' => $to->id]);

        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $suppressor->id,
            'event_type' => 'MOUNT',
            'firearm_id' => $to->id,
            'description' => 'Moved from Range Toy',
        ]);
    }

    public function test_show_exposes_spec_and_mount_fields(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $suppressor = Suppressor::factory()->recycle($this->user)->create([
            'firearm_id' => null,
            'length' => 4.7,
            'weight' => 9.6,
        ]);

        // Mounting via update logs the MOUNT event that "mounted since" reads.
        $suppressor->update(['firearm_id' => $firearm->id]);

        // A cleaning event with a round snapshot drives "last cleaned at X".
        AccessoryEvent::create([
            'user_id' => $this->user->id,
            'accessoryable_type' => $suppressor->getMorphClass(),
            'accessoryable_id' => $suppressor->id,
            'event_type' => 'CLEAN',
            'event_date' => '2024-05-02',
            'rounds' => 1640,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$suppressor->id}")
            ->assertOk()
            ->assertJsonPath('data.length', '4.70')
            ->assertJsonPath('data.weight', '9.60')
            ->assertJsonPath('data.last_cleaned_rounds', 1640)
            ->assertJsonPath('data.mounted_since', now()->toDateString());
    }

    public function test_rounds_through_can_counts_suppressed_session_lines(): void
    {
        $suppressor = Suppressor::factory()->recycle($this->user)->create(['firearm_id' => null]);
        $session = TrainingSession::factory()->recycle($this->user)->create();

        SessionLine::factory()->recycle($this->user)->create([
            'training_session_id' => $session->id,
            'suppressor_id' => $suppressor->id,
            'add_suppressor_count' => true,
            'rounds' => 250,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$suppressor->id}")
            ->assertOk()
            ->assertJsonPath('data.rounds_fired', 250);
    }
}
