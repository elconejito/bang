<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\SessionLine;
use App\Models\Suppressor;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SuppressorEventTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Suppressor $suppressor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        // Created without a firearm so the observer doesn't log an extra MOUNT.
        $this->suppressor = Suppressor::factory()->recycle($this->user)->create(['firearm_id' => null]);
    }

    private function logRangeSession(string $date, int $rounds): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create(['session_date' => $date]);

        SessionLine::factory()->recycle($this->user)->create([
            'training_session_id' => $session->id,
            'suppressor_id' => $this->suppressor->id,
            'add_suppressor_count' => true,
            'rounds' => $rounds,
        ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson("/suppressors/{$this->suppressor->id}/events")->assertUnauthorized();
    }

    public function test_feed_merges_range_entries_with_logged_events(): void
    {
        $this->logRangeSession('2024-05-01', 250);

        // The factory "created" event (ADDED) is already present.
        $data = $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$this->suppressor->id}/events")
            ->assertOk()
            ->json('data');

        $types = collect($data)->pluck('type');
        $this->assertTrue($types->contains('RANGE'));
        $this->assertTrue($types->contains('ADDED'));

        $range = collect($data)->firstWhere('type', 'RANGE');
        $this->assertStringContainsString('250 rounds', $range['title']);
        $this->assertStringContainsString('Running total', $range['subtitle']);
    }

    public function test_feed_filters_by_group(): void
    {
        $this->logRangeSession('2024-05-01', 100);

        $data = $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$this->suppressor->id}/events?filter[group]=range")
            ->assertOk()
            ->json('data');

        $this->assertNotEmpty($data);
        $this->assertTrue(collect($data)->every(fn ($e) => $e['type'] === 'RANGE'));
    }

    public function test_feed_sorts_and_paginates(): void
    {
        $this->logRangeSession('2024-01-01', 100);
        $this->logRangeSession('2024-06-01', 100);

        // Oldest first, range only, one per page.
        $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$this->suppressor->id}/events?filter[group]=range&sort=date&per_page=1&page=1")
            ->assertOk()
            ->assertJsonPath('data.0.date', '2024-01-01')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.last_page', 2);

        $this->actingAs($this->user, 'api')
            ->getJson("/suppressors/{$this->suppressor->id}/events?filter[group]=range&sort=-date&per_page=1&page=1")
            ->assertOk()
            ->assertJsonPath('data.0.date', '2024-06-01');
    }

    public function test_store_snapshots_rounds_onto_clean_events(): void
    {
        $this->logRangeSession('2024-05-01', 300);

        $this->actingAs($this->user, 'api')
            ->postJson("/suppressors/{$this->suppressor->id}/events", [
                'event_type' => 'CLEAN',
                'event_date' => '2024-05-02',
            ])
            ->assertCreated()
            ->assertJsonPath('data.type', 'CLEAN');

        $this->assertDatabaseHas('cms.accessory_events', [
            'accessoryable_id' => $this->suppressor->id,
            'event_type' => 'CLEAN',
            'rounds' => 300,
        ]);
    }
}
