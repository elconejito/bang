<?php

namespace Tests\Feature\API;

use App\Models\Location;
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
            ->postJson('/locations', ['label' => 'Home Safe'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Home Safe');

        $this->assertDatabaseHas('cms.locations', ['label' => 'Home Safe', 'user_id' => $this->user->id]);
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

    public function test_destroy_returns_404_for_another_users_location(): void
    {
        $other = Location::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/locations/{$other->id}")
            ->assertNotFound();
    }
}
