<?php

namespace Tests\Feature\API;

use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // index

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/training')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_sessions(): void
    {
        TrainingSession::factory()->recycle($this->user)->create();
        TrainingSession::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/training')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/training', [])->assertUnauthorized();
    }

    public function test_store_creates_training_session(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/training', [
                'label' => 'Sunday Range Day',
                'session_date' => '2024-03-10',
            ])
            ->assertOk()
            ->assertJsonPath('data.label', 'Sunday Range Day')
            ->assertJsonPath('data.total_rounds', 0)
            ->assertJsonStructure(['data' => ['id', 'label', 'session_date', 'lines', 'total_rounds']]);

        $this->assertDatabaseHas('cms.training_sessions', [
            'label' => 'Sunday Range Day',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/training', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label', 'session_date']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();
        $this->getJson("/training/{$session->id}")->assertUnauthorized();
    }

    public function test_show_returns_training_session(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/training/{$session->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $session->id);
    }

    public function test_show_returns_404_for_another_users_session(): void
    {
        $other = TrainingSession::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/training/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();
        $this->putJson("/training/{$session->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_training_session(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/training/{$session->id}", ['label' => 'Updated Session'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Updated Session');
    }

    public function test_update_returns_404_for_another_users_session(): void
    {
        $other = TrainingSession::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/training/{$other->id}", ['label' => 'X'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();
        $this->deleteJson("/training/{$session->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_training_session(): void
    {
        $session = TrainingSession::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/training/{$session->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.training_sessions', ['id' => $session->id]);
    }

    public function test_destroy_returns_404_for_another_users_session(): void
    {
        $other = TrainingSession::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/training/{$other->id}")
            ->assertNotFound();
    }
}
