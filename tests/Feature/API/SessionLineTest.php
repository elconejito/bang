<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\SessionLine;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SessionLineTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private TrainingSession $session;

    private Firearm $firearm;

    private Ammunition $ammunition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->session = TrainingSession::factory()->recycle($this->user)->create();
        $this->firearm = Firearm::factory()->recycle($this->user)->create();
        $this->ammunition = Ammunition::factory()->recycle($this->user)->create();
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson("/training/{$this->session->id}/lines", [])->assertUnauthorized();
    }

    public function test_store_creates_session_line(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/training/{$this->session->id}/lines", [
                'firearm_id' => $this->firearm->id,
                'ammunition_id' => $this->ammunition->id,
                'rounds' => 150,
                'deduct_ammo' => false,
                'add_firearm_count' => true,
                'add_suppressor_count' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.rounds', 150)
            ->assertJsonPath('data.training_session_id', $this->session->id);

        $this->assertDatabaseHas('cms.session_lines', [
            'training_session_id' => $this->session->id,
            'firearm_id' => $this->firearm->id,
            'rounds' => 150,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_creates_inventory_deduction_when_deduct_ammo_is_true(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/training/{$this->session->id}/lines", [
                'firearm_id' => $this->firearm->id,
                'ammunition_id' => $this->ammunition->id,
                'rounds' => 200,
                'deduct_ammo' => true,
            ])
            ->assertOk();

        $this->assertDatabaseHas('cms.inventories', [
            'ammunition_id' => $this->ammunition->id,
            'rounds' => -200,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_does_not_create_inventory_deduction_when_deduct_ammo_is_false(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/training/{$this->session->id}/lines", [
                'firearm_id' => $this->firearm->id,
                'ammunition_id' => $this->ammunition->id,
                'rounds' => 100,
                'deduct_ammo' => false,
            ])
            ->assertOk();

        $this->assertDatabaseMissing('cms.inventories', [
            'ammunition_id' => $this->ammunition->id,
        ]);
    }

    public function test_store_returns_404_for_another_users_session(): void
    {
        $other = TrainingSession::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson("/training/{$other->id}/lines", [
                'firearm_id' => $this->firearm->id,
                'ammunition_id' => $this->ammunition->id,
                'rounds' => 100,
            ])
            ->assertNotFound();
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/training/{$this->session->id}/lines", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['firearm_id', 'ammunition_id', 'rounds']);
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create();
        $this->putJson("/training/{$this->session->id}/lines/{$line->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_session_line(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create(['rounds' => 100, 'deduct_ammo' => false]);

        $this->actingAs($this->user, 'api')
            ->putJson("/training/{$this->session->id}/lines/{$line->id}", ['rounds' => 200])
            ->assertOk()
            ->assertJsonPath('data.rounds', 200);
    }

    public function test_update_creates_inventory_deduction_when_toggled_on(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create([
            'ammunition_id' => $this->ammunition->id,
            'rounds' => 100,
            'deduct_ammo' => false,
        ]);

        $this->actingAs($this->user, 'api')
            ->putJson("/training/{$this->session->id}/lines/{$line->id}", ['deduct_ammo' => true])
            ->assertOk();

        $this->assertDatabaseHas('cms.inventories', [
            'ammunition_id' => $this->ammunition->id,
            'rounds' => -100,
        ]);
    }

    public function test_update_removes_inventory_deduction_when_toggled_off(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create([
            'ammunition_id' => $this->ammunition->id,
            'rounds' => 100,
            'deduct_ammo' => true,
        ]);

        // Create the inventory deduction record manually
        Inventory::factory()->create([
            'ammunition_id' => $this->ammunition->id,
            'rounds' => -100,
            'session_line_id' => $line->id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->putJson("/training/{$this->session->id}/lines/{$line->id}", ['deduct_ammo' => false])
            ->assertOk();

        $this->assertDatabaseMissing('cms.inventories', ['session_line_id' => $line->id]);
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create();
        $this->deleteJson("/training/{$this->session->id}/lines/{$line->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_session_line(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create(['deduct_ammo' => false]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/training/{$this->session->id}/lines/{$line->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.session_lines', ['id' => $line->id]);
    }

    public function test_destroy_also_deletes_inventory_deduction(): void
    {
        $line = SessionLine::factory()->recycle($this->user)->for($this->session)->create([
            'ammunition_id' => $this->ammunition->id,
            'rounds' => 100,
            'deduct_ammo' => true,
        ]);

        Inventory::factory()->create([
            'ammunition_id' => $this->ammunition->id,
            'rounds' => -100,
            'session_line_id' => $line->id,
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/training/{$this->session->id}/lines/{$line->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.inventories', ['session_line_id' => $line->id]);
    }
}
