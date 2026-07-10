<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MagazineTest extends TestCase
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
        $this->getJson('/magazines')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_magazines(): void
    {
        Magazine::factory()->recycle($this->user)->create();
        Magazine::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/magazines')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/magazines', [])->assertUnauthorized();
    }

    public function test_store_creates_magazine(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/magazines', [
                'manufacturer' => 'Magpul',
                'capacity' => 17,
                'label' => 'Primary',
            ])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Magpul');

        $this->assertDatabaseHas('cms.magazines', ['manufacturer' => 'Magpul', 'user_id' => $this->user->id]);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();
        $this->getJson("/magazines/{$magazine->id}")->assertUnauthorized();
    }

    public function test_show_returns_magazine(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/magazines/{$magazine->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $magazine->id);
    }

    public function test_show_returns_404_for_another_users_magazine(): void
    {
        $other = Magazine::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/magazines/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();
        $this->putJson("/magazines/{$magazine->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_magazine(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/magazines/{$magazine->id}", ['manufacturer' => 'Glock OEM', 'capacity' => 15])
            ->assertOk()
            ->assertJsonPath('data.manufacturer', 'Glock OEM');
    }

    public function test_update_returns_404_for_another_users_magazine(): void
    {
        $other = Magazine::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/magazines/{$other->id}", ['manufacturer' => 'X'])
            ->assertNotFound();
    }

    public function test_state_change_loads_and_places_a_magazine_without_changing_ammunition_inventory(): void
    {
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $ammunition = Ammunition::factory()->recycle($this->user)->create([
            'caliber_id' => $caliber->id,
            'inventory' => 500,
        ]);
        $location = Location::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create(['capacity' => 17]);
        $magazine->calibers()->attach($caliber);
        $inventoryEntries = $ammunition->inventories()->count();

        $this->actingAs($this->user, 'api')
            ->patchJson("/magazines/{$magazine->id}/state", [
                'location_id' => $location->id,
                'current_firearm_id' => null,
                'loaded_ammunition_id' => $ammunition->id,
                'loaded_rounds' => 15,
            ])
            ->assertOk()
            ->assertJsonPath('data.display_status', 'loaded')
            ->assertJsonPath('data.loaded_rounds', 15)
            ->assertJsonPath('data.location.id', $location->id);

        $this->assertSame(500, $ammunition->fresh()->inventory);
        $this->assertSame($inventoryEntries, $ammunition->inventories()->count());
    }

    public function test_state_change_rejects_invalid_load_and_placement_combinations(): void
    {
        $location = Location::factory()->recycle($this->user)->create();
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create(['capacity' => 10]);
        $magazine->compatibleFirearms()->attach($firearm);

        $this->actingAs($this->user, 'api')
            ->patchJson("/magazines/{$magazine->id}/state", [
                'location_id' => $location->id,
                'current_firearm_id' => $firearm->id,
                'loaded_ammunition_id' => null,
                'loaded_rounds' => 11,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['location_id', 'loaded_rounds', 'loaded_ammunition_id']);
    }

    public function test_state_change_rejects_an_incompatible_firearm(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $magazine = Magazine::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->patchJson("/magazines/{$magazine->id}/state", [
                'location_id' => null,
                'current_firearm_id' => $firearm->id,
                'loaded_ammunition_id' => null,
                'loaded_rounds' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_firearm_id');
    }

    public function test_state_change_rejects_a_firearm_that_already_has_a_magazine_inserted(): void
    {
        $firearm = Firearm::factory()->recycle($this->user)->create();
        $inserted = Magazine::factory()->recycle($this->user)->create(['current_firearm_id' => $firearm->id]);
        $inserted->compatibleFirearms()->attach($firearm);
        $magazine = Magazine::factory()->recycle($this->user)->create();
        $magazine->compatibleFirearms()->attach($firearm);

        $this->actingAs($this->user, 'api')
            ->patchJson("/magazines/{$magazine->id}/state", [
                'location_id' => null,
                'current_firearm_id' => $firearm->id,
                'loaded_ammunition_id' => null,
                'loaded_rounds' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_firearm_id');

        $this->assertNull($magazine->fresh()->current_firearm_id);
    }

    public function test_state_change_rejects_another_users_location(): void
    {
        $location = Location::factory()->create();
        $magazine = Magazine::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->patchJson("/magazines/{$magazine->id}/state", [
                'location_id' => $location->id,
                'current_firearm_id' => null,
                'loaded_ammunition_id' => null,
                'loaded_rounds' => 0,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('location_id');
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();
        $this->deleteJson("/magazines/{$magazine->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_magazine(): void
    {
        $magazine = Magazine::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/magazines/{$magazine->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.magazines', ['id' => $magazine->id]);
    }

    public function test_destroy_returns_404_for_another_users_magazine(): void
    {
        $other = Magazine::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/magazines/{$other->id}")
            ->assertNotFound();
    }
}
