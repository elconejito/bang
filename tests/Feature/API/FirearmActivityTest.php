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

class FirearmActivityTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Firearm $firearm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->firearm = Firearm::factory()->recycle($this->user)->create();
    }

    private function createRangeSession(string $date): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create(['session_date' => $date]);

        SessionLine::factory()->recycle($this->user)->create([
            'training_session_id' => $session->id,
            'firearm_id' => $this->firearm->id,
        ]);
    }

    private function createMountEvent(string $date): void
    {
        $suppressor = Suppressor::factory()->recycle($this->user)->create();

        AccessoryEvent::create([
            'user_id' => $this->user->id,
            'firearm_id' => $this->firearm->id,
            'event_type' => 'mount',
            'event_date' => $date,
            'accessoryable_type' => $suppressor->getMorphClass(),
            'accessoryable_id' => $suppressor->id,
        ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson("/firearms/{$this->firearm->id}/activity")->assertUnauthorized();
    }

    public function test_index_paginates_the_activity_feed(): void
    {
        $this->createRangeSession('2024-01-01');
        $this->createRangeSession('2024-02-01');
        $this->createRangeSession('2024-03-01');

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?per_page=2&page=1")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.current_page', 1);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?per_page=2&page=2")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }

    public function test_index_sorts_by_date(): void
    {
        $this->createRangeSession('2024-01-01');
        $this->createRangeSession('2024-06-01');

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?sort=-date")
            ->assertOk()
            ->assertJsonPath('data.0.date', '2024-06-01')
            ->assertJsonPath('data.1.date', '2024-01-01');

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?sort=date")
            ->assertOk()
            ->assertJsonPath('data.0.date', '2024-01-01')
            ->assertJsonPath('data.1.date', '2024-06-01');
    }

    public function test_index_filters_by_type(): void
    {
        $this->createRangeSession('2024-01-01');
        $this->createMountEvent('2024-02-01');

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?filter[type]=RANGE")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'RANGE')
            ->assertJsonPath('meta.total', 1);

        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?filter[type]=MOUNT")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'MOUNT')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_index_header_stats_reflect_full_feed_when_filtered(): void
    {
        $this->createRangeSession('2024-01-01');
        $this->createMountEvent('2024-02-01');

        // range_count stays at the full RANGE total even when filtering to MOUNT.
        $this->actingAs($this->user, 'api')
            ->getJson("/firearms/{$this->firearm->id}/activity?filter[type]=MOUNT")
            ->assertOk()
            ->assertJsonPath('meta.range_count', 1)
            ->assertJsonPath('meta.last_session_date', '2024-01-01');
    }
}
