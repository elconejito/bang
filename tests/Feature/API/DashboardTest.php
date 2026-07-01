<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\SessionLine;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/dashboard')->assertUnauthorized();
    }

    public function test_most_shot_firearms_includes_sessions_older_than_a_year(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $oldSession = TrainingSession::factory()
            ->recycle($this->user)
            ->create(['session_date' => now()->subYears(3)]);

        SessionLine::factory()
            ->recycle($this->user)
            ->for($oldSession)
            ->for($firearm)
            ->create(['rounds' => 250, 'add_firearm_count' => true]);

        $this->actingAs($this->user, 'api')
            ->getJson('/dashboard')
            ->assertOk()
            ->assertJsonPath('data.most_shot_firearms.0.id', $firearm->id)
            ->assertJsonPath('data.most_shot_firearms.0.rounds_total', 250);
    }

    public function test_most_shot_firearms_sums_rounds_across_all_sessions(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();

        foreach ([now()->subYears(5), now()->subMonths(2)] as $date) {
            $session = TrainingSession::factory()->recycle($this->user)->create(['session_date' => $date]);
            SessionLine::factory()
                ->recycle($this->user)
                ->for($session)
                ->for($firearm)
                ->create(['rounds' => 100, 'add_firearm_count' => true]);
        }

        $this->actingAs($this->user, 'api')
            ->getJson('/dashboard')
            ->assertOk()
            ->assertJsonPath('data.most_shot_firearms.0.rounds_total', 200);
    }
}
