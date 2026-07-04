<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Range;
use App\Models\Reference\BulletType;
use App\Models\SessionLine;
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

    public function test_index_filters_sessions_by_exact_range_id(): void
    {
        $selectedRange = Range::create([
            'label' => 'North Bay Range',
            'user_id' => $this->user->id,
        ]);
        $otherRange = Range::create([
            'label' => 'South Bay Range',
            'user_id' => $this->user->id,
        ]);

        $matchingSession = TrainingSession::factory()
            ->recycle($this->user)
            ->create(['range_id' => $selectedRange->id]);

        TrainingSession::factory()
            ->recycle($this->user)
            ->create(['range_id' => $otherRange->id]);

        $this->actingAs($this->user, 'api')
            ->getJson("/training?filter[range_id]={$selectedRange->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingSession->id);
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

    public function test_show_returns_session_line_display_details(): void
    {
        $bulletType = BulletType::query()->forceCreate([
            'label' => 'Full Metal Jacket',
            'abbreviation' => 'FMJ',
            'user_id' => $this->user->id,
        ]);
        $caliber = Caliber::factory()->recycle($this->user)->create([
            'label' => '9mm',
            'caliber' => '9x19',
        ]);
        $firearm = Firearm::factory()->recycle($this->user)->create([
            'manufacturer' => 'Glock',
            'model' => '19 Gen5',
        ]);
        $firearm->calibers()->attach($caliber);

        $ammunition = Ammunition::factory()->recycle($this->user)->create([
            'manufacturer' => 'Federal',
            'label' => 'American Eagle',
            'weight' => 115,
            'bullet_type_id' => $bulletType->id,
        ]);
        $session = TrainingSession::factory()->recycle($this->user)->create();
        SessionLine::factory()
            ->recycle($this->user)
            ->for($session, 'trainingSession')
            ->create([
                'firearm_id' => $firearm->id,
                'ammunition_id' => $ammunition->id,
            ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/training/{$session->id}")
            ->assertOk()
            ->assertJsonPath('data.lines.0.firearm.manufacturer', 'Glock')
            ->assertJsonPath('data.lines.0.firearm.model', '19 Gen5')
            ->assertJsonPath('data.lines.0.firearm.calibers.0.label', '9mm')
            ->assertJsonPath('data.lines.0.ammunition.weight', 115)
            ->assertJsonPath('data.lines.0.ammunition.bullet_type.abbreviation', 'FMJ');
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
