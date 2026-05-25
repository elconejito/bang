<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Ammunition $ammunition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $caliber = Caliber::factory()->recycle($this->user)->create();
        $this->ammunition = Ammunition::factory()->recycle($this->user)->recycle($caliber)->create();
    }

    // index

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/inventories')->assertUnauthorized();
    }

    public function test_index_returns_only_authenticated_users_inventories(): void
    {
        Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();
        Inventory::factory()->create(); // another user

        $this->actingAs($this->user, 'api')
            ->getJson('/inventories')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // store

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/inventories', [])->assertUnauthorized();
    }

    public function test_store_creates_inventory_entry(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/inventories', [
                'ammunition_id' => $this->ammunition->id,
                'inventory_date' => '2024-01-15',
                'rounds' => 200,
                'is_purchase' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.rounds', 200);

        $this->assertDatabaseHas('cms.inventories', [
            'ammunition_id' => $this->ammunition->id,
            'rounds' => 200,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_creates_inventory_with_order_when_purchase(): void
    {
        $store = Store::factory()->recycle($this->user)->create();

        $this->actingAs($this->user, 'api')
            ->postJson('/inventories', [
                'ammunition_id' => $this->ammunition->id,
                'inventory_date' => '2024-01-15',
                'rounds' => 100,
                'is_purchase' => true,
                'cost' => 49.99,
                'store_id' => $store->id,
            ])
            ->assertOk();

        $this->assertDatabaseHas('cms.inventories', [
            'ammunition_id' => $this->ammunition->id,
            'rounds' => 100,
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseHas('cms.orders', [
            'rounds' => 100,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'api')
            ->postJson('/inventories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ammunition_id', 'rounds', 'is_purchase']);
    }

    // show

    public function test_show_requires_authentication(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();
        $this->getJson("/inventories/{$inventory->id}")->assertUnauthorized();
    }

    public function test_show_returns_inventory(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/inventories/{$inventory->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $inventory->id);
    }

    public function test_show_returns_404_for_another_users_inventory(): void
    {
        $other = Inventory::factory()->create();

        $this->actingAs($this->user, 'api')
            ->getJson("/inventories/{$other->id}")
            ->assertNotFound();
    }

    // destroy

    public function test_destroy_requires_authentication(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();
        $this->deleteJson("/inventories/{$inventory->id}")->assertUnauthorized();
    }

    public function test_destroy_deletes_inventory(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/inventories/{$inventory->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.inventories', ['id' => $inventory->id]);
    }

    public function test_destroy_returns_404_for_another_users_inventory(): void
    {
        $other = Inventory::factory()->create();

        $this->actingAs($this->user, 'api')
            ->deleteJson("/inventories/{$other->id}")
            ->assertNotFound();
    }

    // update returns 501 (not yet implemented)

    public function test_update_returns_not_implemented(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();

        $this->actingAs($this->user, 'api')
            ->putJson("/inventories/{$inventory->id}", [])
            ->assertStatus(501);
    }
}
