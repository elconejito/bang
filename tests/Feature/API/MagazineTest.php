<?php

namespace Tests\Feature\API;

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
