<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\SessionLine;
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

    public function test_index_filters_by_type(): void
    {
        $order = Order::create([
            'order_date' => '2024-01-01',
            'rounds' => 100,
            'total_cost' => 50,
            'user_id' => $this->user->id,
        ]);
        $buy = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create(['order_id' => $order->id]);

        $sessionLine = SessionLine::factory()->recycle($this->user)->create();
        $fired = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create(['session_line_id' => $sessionLine->id]);

        $adjust = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create();

        $filterUrl = fn (string $type) => "/inventories?filter[ammunition_id]={$this->ammunition->id}&filter[type]={$type}";

        $this->actingAs($this->user, 'api')
            ->getJson($filterUrl('BUY'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $buy->id)
            ->assertJsonPath('data.0.order_id', $order->id);

        $this->actingAs($this->user, 'api')
            ->getJson($filterUrl('FIRED'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $fired->id);

        $this->actingAs($this->user, 'api')
            ->getJson($filterUrl('ADJUST'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $adjust->id);
    }

    public function test_index_sorts_by_inventory_date(): void
    {
        $older = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create(['inventory_date' => '2024-01-01']);
        $newer = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create(['inventory_date' => '2024-06-01']);

        $this->actingAs($this->user, 'api')
            ->getJson("/inventories?filter[ammunition_id]={$this->ammunition->id}&sort=-inventory_date,rounds")
            ->assertOk()
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id);

        $this->actingAs($this->user, 'api')
            ->getJson("/inventories?filter[ammunition_id]={$this->ammunition->id}&sort=inventory_date,rounds")
            ->assertOk()
            ->assertJsonPath('data.0.id', $older->id)
            ->assertJsonPath('data.1.id', $newer->id);
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

    public function test_update_changes_an_adjustment_and_recalculates_ammunition(): void
    {
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'rounds' => 10,
            'inventory_date' => '2024-01-01',
        ]);

        $this->actingAs($this->user, 'api')
            ->putJson("/inventories/{$inventory->id}", [
                'rounds' => 25,
                'inventory_date' => '2024-02-01',
            ])
            ->assertOk()
            ->assertJsonPath('data.rounds', 25)
            ->assertJsonPath('data.inventory_date', '2024-02-01');

        $this->assertDatabaseHas('cms.inventories', [
            'id' => $inventory->id,
            'rounds' => 25,
            'inventory_date' => '2024-02-01 00:00:00',
        ]);
        $this->assertSame(25, $this->ammunition->refresh()->inventory);
    }

    public function test_update_rejects_range_session_inventory(): void
    {
        $sessionLine = SessionLine::factory()->recycle($this->user)->create();
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'session_line_id' => $sessionLine->id,
        ]);

        $this->actingAs($this->user, 'api')
            ->putJson("/inventories/{$inventory->id}", [
                'rounds' => -25,
                'inventory_date' => '2024-02-01',
            ])
            ->assertUnprocessable();
    }

    public function test_update_changes_purchase_details_with_a_store(): void
    {
        $store = Store::factory()->recycle($this->user)->create();
        $order = Order::create([
            'order_date' => '2024-01-01',
            'rounds' => 100,
            'total_cost' => 50,
            'user_id' => $this->user->id,
        ]);
        $inventory = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'order_id' => $order->id,
            'rounds' => 100,
            'cost' => 50,
        ]);

        $this->actingAs($this->user, 'api')
            ->putJson("/inventories/{$inventory->id}", [
                'rounds' => 120,
                'inventory_date' => '2024-02-01',
                'cost' => 60,
                'store_id' => $store->id,
                'order_ref' => 'UPDATED-1',
            ])
            ->assertOk()
            ->assertJsonPath('data.store_id', $store->id)
            ->assertJsonPath('data.order_ref', 'UPDATED-1');

        $this->assertDatabaseHas('cms.orders', [
            'id' => $order->id,
            'rounds' => 120,
            'total_cost' => 60,
            'store_id' => $store->id,
            'order_ref' => 'UPDATED-1',
        ]);
    }

    public function test_updating_one_purchase_line_recalculates_the_whole_order(): void
    {
        $order = Order::create([
            'order_date' => '2024-01-01',
            'rounds' => 300,
            'total_cost' => 90,
            'user_id' => $this->user->id,
        ]);
        $firstLine = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'order_id' => $order->id,
            'rounds' => 100,
            'cost' => 30,
        ]);
        Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'order_id' => $order->id,
            'rounds' => 200,
            'cost' => 60,
        ]);

        $this->actingAs($this->user, 'api')->putJson("/inventories/{$firstLine->id}", [
            'rounds' => 150,
            'inventory_date' => '2024-02-01',
            'cost' => 45,
            'store_id' => null,
            'order_ref' => 'MULTI-LINE',
        ])->assertOk();

        $order->refresh();
        $this->assertSame(350, $order->rounds);
        $this->assertSame(105.0, (float) $order->total_cost);
        $this->assertSame(350, $this->ammunition->refresh()->inventory);
    }

    public function test_deleting_purchase_lines_recalculates_then_removes_empty_order(): void
    {
        $order = Order::create([
            'order_date' => '2024-01-01',
            'rounds' => 300,
            'total_cost' => 90,
            'user_id' => $this->user->id,
        ]);
        $firstLine = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'order_id' => $order->id,
            'rounds' => 100,
            'cost' => 30,
        ]);
        $secondLine = Inventory::factory()->recycle($this->user)->recycle($this->ammunition)->create([
            'order_id' => $order->id,
            'rounds' => 200,
            'cost' => 60,
        ]);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/inventories/{$firstLine->id}")
            ->assertNoContent();

        $order->refresh();
        $this->assertSame(200, $order->rounds);
        $this->assertSame(60.0, (float) $order->total_cost);

        $this->actingAs($this->user, 'api')
            ->deleteJson("/inventories/{$secondLine->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('cms.orders', ['id' => $order->id]);
        $this->assertSame(0, $this->ammunition->refresh()->inventory);
    }
}
