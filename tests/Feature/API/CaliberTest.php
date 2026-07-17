<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Reference\CaliberType;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CaliberTest extends TestCase
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
        $this->getJson('/calibers')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_calibers(): void
    {
        Caliber::factory()->recycle($this->user)->create();
        Caliber::factory()->create(); // another user's caliber

        $this->actingAs($this->user, 'api')
            ->getJson('/calibers')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_includes_usage_counts(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $caliber->firearms()->attach($firearm);
        Ammunition::factory()->count(2)->recycle($this->user)->create(['caliber_id' => $caliber->id]);

        $this->actingAs($this->user, 'api')
            ->getJson('/calibers')
            ->assertOk()
            ->assertJsonPath('data.0.firearms_count', 1)
            ->assertJsonPath('data.0.loads_count', 2);
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/calibers', [])->assertUnauthorized();
    }

    public function test_store_creates_caliber(): void
    {
        $caliberType = CaliberType::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson('/calibers', [
                'caliber' => '9mm',
                'caliber_type_id' => $caliberType->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.caliber', '9mm');

        $this->assertDatabaseHas('cms.calibers', ['caliber' => '9mm', 'user_id' => $this->user->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/calibers', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['caliber', 'caliber_type_id']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $this->getJson("/calibers/{$caliber->id}")->assertUnauthorized();
    }

    public function test_show_returns_caliber(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/calibers/{$caliber->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $caliber->id);
    }

    public function test_show_returns_404_for_another_users_caliber(): void
    {
        $other = Caliber::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/calibers/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $this->putJson("/calibers/{$caliber->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_caliber(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/calibers/{$caliber->id}", ['caliber' => '10mm'])
            ->assertOk()
            ->assertJsonPath('data.caliber', '10mm');
    }

    public function test_update_returns_404_for_another_users_caliber(): void
    {
        $other = Caliber::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/calibers/{$other->id}", ['caliber' => '10mm'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $this->deleteJson("/calibers/{$caliber->id}")->assertUnauthorized();
    }

    public function test_destroy_soft_deletes_unused_caliber(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/calibers/{$caliber->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('cms.calibers', ['id' => $caliber->id]);
    }

    public function test_destroy_is_blocked_when_caliber_has_loads(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        Ammunition::factory()->recycle($this->user)->create(['caliber_id' => $caliber->id]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/calibers/{$caliber->id}")
            ->assertStatus(409);

        $this->assertNotSoftDeleted('cms.calibers', ['id' => $caliber->id]);
    }

    public function test_destroy_is_blocked_when_caliber_is_used_by_firearm(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $caliber->firearms()->attach($firearm);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/calibers/{$caliber->id}")
            ->assertStatus(409);

        $this->assertNotSoftDeleted('cms.calibers', ['id' => $caliber->id]);
    }

    public function test_destroy_returns_404_for_another_users_caliber(): void
    {
        $other = Caliber::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/calibers/{$other->id}")
            ->assertNotFound();
    }

    // total

    public function test_total_requires_authentication(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $this->getJson("/calibers/{$caliber->id}/total")->assertUnauthorized();
    }

    public function test_total_returns_inventory_summary(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/calibers/{$caliber->id}/total")
            ->assertOk()
            ->assertJsonStructure(['data' => ['total']]);
    }
}
