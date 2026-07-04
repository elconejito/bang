<?php

namespace Tests\Feature\API;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
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
        $this->getJson('/stores')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_stores(): void
    {
        Store::factory()->recycle($this->user)->create();
        Store::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/stores')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/stores', [])->assertUnauthorized();
    }

    public function test_store_creates_store(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/stores', ['label' => 'Cabelas'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Cabelas');

        $this->assertDatabaseHas('cms.stores', ['label' => 'Cabelas', 'user_id' => $this->user->id]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/stores', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $store = Store::factory()->recycle($this->user)->create();
        $this->getJson("/stores/{$store->id}")->assertUnauthorized();
    }

    public function test_show_returns_store(): void
    {
        $store = Store::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/stores/{$store->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $store->id);
    }

    public function test_show_returns_404_for_another_users_store(): void
    {
        $other = Store::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/stores/{$other->id}")
            ->assertNotFound();
    }

    // update

    public function test_update_requires_authentication(): void
    {
        $store = Store::factory()->recycle($this->user)->create();
        $this->putJson("/stores/{$store->id}", [])->assertUnauthorized();
    }

    public function test_update_modifies_store(): void
    {
        $store = Store::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/stores/{$store->id}", ['label' => 'Bass Pro'])
            ->assertOk()
            ->assertJsonPath('data.label', 'Bass Pro');
    }

    public function test_update_returns_404_for_another_users_store(): void
    {
        $other = Store::factory()->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/stores/{$other->id}", ['label' => 'X'])
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $store = Store::factory()->recycle($this->user)->create();
        $this->deleteJson("/stores/{$store->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_store(): void
    {
        $store = Store::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/stores/{$store->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.stores', ['id' => $store->id]);
    }

    public function test_destroy_returns_404_for_another_users_store(): void
    {
        $other = Store::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/stores/{$other->id}")
            ->assertNotFound();
    }
}
