<?php

namespace Tests\Feature\API;

use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
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
        $this->getJson('/locations')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_locations(): void
    {
        Location::factory()->recycle($this->user)->create();
        Location::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/locations')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/locations', [])->assertUnauthorized();
    }

    public function test_store_creates_location(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/locations', ['label' => 'Home Safe', 'description' => 'Main storage cabinet'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Home Safe')
            ->assertJsonPath('data.description', 'Main storage cabinet');

        $this->assertDatabaseHas('cms.locations', ['label' => 'Home Safe', 'user_id' => $this->user->id]);
    }

    public function test_store_creates_a_sublocation_and_returns_its_full_label(): void
    {
        $parent = Location::factory()->recycle($this->user)->create(['label' => 'Gun Safe']);

        $this->actingAs($this->user, 'api')
            ->postJson('/locations', [
                'label' => 'Top Shelf',
                'parent_location_id' => $parent->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.parent_location_id', $parent->id)
            ->assertJsonPath('data.parent.label', 'Gun Safe')
            ->assertJsonPath('data.full_label', 'Gun Safe › Top Shelf');
    }

    public function test_store_rejects_another_users_parent_location(): void
    {
        $otherUsersLocation = Location::factory()->create();

        $this->actingAs($this->user, 'api')
            ->postJson('/locations', [
                'label' => 'Top Shelf',
                'parent_location_id' => $otherUsersLocation->id,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_location_id');
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/locations', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $location = Location::factory()->recycle($this->user)->create();
        $this->getJson("/locations/{$location->id}")->assertUnauthorized();
    }

    public function test_show_returns_location(): void
    {
        $location = Location::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/locations/{$location->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $location->id);
    }

    public function test_show_includes_magazines_stored_at_location(): void
    {
        $location = Location::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create([
            'location_id' => $location->id,
            'id_marking' => 'SAFE-01',
            'loaded_rounds' => 0,
        ]);

        $this->actingAs($this->user, 'api')
            ->getJson("/locations/{$location->id}")
            ->assertOk()
            ->assertJsonPath('data.contents.magazines.0.id', $magazine->id)
            ->assertJsonPath('data.contents.magazines.0.id_marking', 'SAFE-01');
    }

    public function test_show_returns_404_for_another_users_location(): void
    {
        $other = Location::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/locations/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $location = Location::factory()->recycle($this->user)->create();
        $this->putJson("/locations/{$location->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_location(): void
    {
        $location = Location::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/locations/{$location->id}", ['label' => 'Gun Safe'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Gun Safe');
    }

    public function test_update_can_move_a_location_and_rejects_hierarchy_cycles(): void
    {
        $root = Location::factory()->recycle($this->user)->create(['label' => 'Gun Room']);
        $safe = Location::factory()->childOf($root)->create(['label' => 'Gun Safe']);
        $shelf = Location::factory()->childOf($safe)->create(['label' => 'Top Shelf']);
        $otherRoot = Location::factory()->recycle($this->user)->create(['label' => 'Workshop']);

        $this->actingAs($this->user, 'api')
            ->putJson("/locations/{$root->id}", ['parent_location_id' => $shelf->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_location_id');

        $this->actingAs($this->user, 'api')
            ->putJson("/locations/{$safe->id}", ['parent_location_id' => $otherRoot->id])
            ->assertOk()
            ->assertJsonPath('data.full_label', 'Workshop › Gun Safe');

        $this->actingAs($this->user, 'api')
            ->putJson("/locations/{$root->id}", ['parent_location_id' => $root->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('parent_location_id');
    }

    public function test_update_returns_404_for_another_users_location(): void
    {
        $other = Location::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/locations/{$other->id}", ['label' => 'X'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $location = Location::factory()->recycle($this->user)->create();
        $this->deleteJson("/locations/{$location->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_location(): void
    {
        $location = Location::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/locations/{$location->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.locations', ['id' => $location->id]);
    }

    public function test_destroy_is_blocked_while_location_has_sublocations_or_assets(): void
    {
        $parent = Location::factory()->recycle($this->user)->create();
        $child = Location::factory()->childOf($parent)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/locations/{$parent->id}")
            ->assertConflict()
            ->assertJsonPath('code', 'location_delete_blocked');

        $child->delete();
        Firearm::factory()->recycle($this->user)->create(['location_id' => $parent->id]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/locations/{$parent->id}")
            ->assertConflict()
            ->assertJsonPath('code', 'location_delete_blocked');
    }

    public function test_destroy_returns_404_for_another_users_location(): void
    {
        $other = Location::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/locations/{$other->id}")
            ->assertNotFound();
    }
}
