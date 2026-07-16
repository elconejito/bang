<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Reference\Purpose;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PurposeTest extends TestCase
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
        $this->getJson('/purpose')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_purposes(): void
    {
        Purpose::factory()->recycle($this->user)->create();
        Purpose::factory()->create(); // another user's purpose

        $this->actingAs($this->user, 'api')
            ->getJson('/purpose')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_includes_loads_count(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();
        Ammunition::factory()->count(3)->recycle($this->user)->create(['purpose_id' => $purpose->id]);

        $this->actingAs($this->user, 'api')
            ->getJson('/purpose')
            ->assertOk()
            ->assertJsonPath('data.0.loads_count', 3);
    }

    public function test_total_rounds_sums_inventory_for_all_purpose_loads(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();
        Ammunition::factory()->recycle($this->user)->create([
            'purpose_id' => $purpose->id,
            'inventory' => 40,
        ]);
        Ammunition::factory()->recycle($this->user)->create([
            'purpose_id' => $purpose->id,
            'inventory' => 60,
        ]);

        $this->actingAs($this->user);

        $this->assertSame(100, $purpose->totalRounds());
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/purpose', [])->assertUnauthorized();
    }

    public function test_store_creates_purpose(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/purpose', ['label' => 'Duty'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Duty');

        $this->assertDatabaseHas('reference.purposes', [
            'label' => 'Duty',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_label(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/purpose', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }

    // update

    public function test_update_modifies_purpose(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/purpose/{$purpose->id}", ['label' => 'Renamed'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Renamed');

        $this->assertDatabaseHas('reference.purposes', ['id' => $purpose->id, 'label' => 'Renamed']);
    }

    public function test_update_returns_404_for_another_users_purpose(): void
    {
        $other = Purpose::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/purpose/{$other->id}", ['label' => 'Nope'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_soft_deletes_unused_purpose(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/purpose/{$purpose->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('reference.purposes', ['id' => $purpose->id]);
    }

    public function test_destroy_is_blocked_when_purpose_has_loads(): void
    {
        $purpose = Purpose::factory()->recycle($this->user)->create();
        Ammunition::factory()->recycle($this->user)->create(['purpose_id' => $purpose->id]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/purpose/{$purpose->id}")
            ->assertStatus(409);

        $this->assertNotSoftDeleted('reference.purposes', ['id' => $purpose->id]);
    }

    public function test_destroy_returns_404_for_another_users_purpose(): void
    {
        $other = Purpose::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/purpose/{$other->id}")
            ->assertNotFound();
    }
}
