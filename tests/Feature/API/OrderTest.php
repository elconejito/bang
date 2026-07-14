<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Inventory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_create_view_and_update_a_multi_line_order(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->recycle($user)->create();
        $caliber = Caliber::factory()->recycle($user)->create();
        $firstAmmo = Ammunition::factory()->recycle($user)->recycle($caliber)->create();
        $secondAmmo = Ammunition::factory()->recycle($user)->recycle($caliber)->create();

        $response = $this->actingAs($user, 'api')->postJson('/orders', [
            'store_id' => $store->id,
            'order_date' => '2026-07-12',
            'order_ref' => 'STORE-A-100',
            'items' => [
                ['ammunition_id' => $firstAmmo->id, 'rounds' => 100, 'cost' => 12.50],
                ['ammunition_id' => $secondAmmo->id, 'rounds' => 200, 'cost' => 75.25],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.rounds', 300)
            ->assertJsonPath('data.total_cost', 87.75)
            ->assertJsonPath('data.items.0.type', 'ammunition')
            ->assertJsonCount(2, 'data.items');

        $orderId = $response->json('data.id');
        $firstItemId = $response->json('data.items.0.id');

        $this->assertSame(100, $firstAmmo->refresh()->inventory);
        $this->assertSame(200, $secondAmmo->refresh()->inventory);

        $this->actingAs($user, 'api')
            ->getJson("/orders/{$orderId}")
            ->assertOk()
            ->assertJsonPath('data.store.id', $store->id)
            ->assertJsonPath('data.items.0.ammunition_id', $firstAmmo->id)
            ->assertJsonPath('data.items.0.ammunition.id', $firstAmmo->id)
            ->assertJsonPath('data.items.1.ammunition.caliber.label', $caliber->label);

        $this->assertSame(
            ['2026-07-12'],
            Inventory::where('order_id', $orderId)
                ->pluck('inventory_date')
                ->map->toDateString()
                ->unique()
                ->values()
                ->all(),
        );

        $this->actingAs($user, 'api')->putJson("/orders/{$orderId}", [
            'store_id' => $store->id,
            'order_date' => '2026-07-13',
            'order_ref' => 'STORE-A-UPDATED',
            'items' => [
                ['id' => $firstItemId, 'ammunition_id' => $firstAmmo->id, 'rounds' => 150, 'cost' => 18.75],
            ],
        ])->assertOk()
            ->assertJsonPath('data.rounds', 150)
            ->assertJsonPath('data.total_cost', 18.75)
            ->assertJsonCount(1, 'data.items');

        $this->assertSame(150, $firstAmmo->refresh()->inventory);
        $this->assertSame(0, $secondAmmo->refresh()->inventory);
    }

    public function test_order_rejects_duplicate_or_another_users_ammunition(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->recycle($user)->create();
        $ammo = Ammunition::factory()->recycle($user)->create();
        $otherAmmo = Ammunition::factory()->create();

        $this->actingAs($user, 'api')->postJson('/orders', [
            'store_id' => $store->id,
            'order_date' => '2026-07-12',
            'items' => [
                ['ammunition_id' => $ammo->id, 'rounds' => 100, 'cost' => 10],
                ['ammunition_id' => $ammo->id, 'rounds' => 100, 'cost' => 10],
            ],
        ])->assertUnprocessable()->assertJsonValidationErrors('items.1.ammunition_id');

        $this->actingAs($user, 'api')->postJson('/orders', [
            'store_id' => $store->id,
            'order_date' => '2026-07-12',
            'items' => [['ammunition_id' => $otherAmmo->id, 'rounds' => 100, 'cost' => 10]],
        ])->assertUnprocessable()->assertJsonValidationErrors('items.0.ammunition_id');
    }

    public function test_deleting_an_order_removes_lines_and_recalculates_inventory(): void
    {
        $user = User::factory()->create();
        $ammo = Ammunition::factory()->recycle($user)->create();

        $response = $this->actingAs($user, 'api')->postJson('/orders', [
            'order_date' => '2026-07-12',
            'items' => [['ammunition_id' => $ammo->id, 'rounds' => 100, 'cost' => 10]],
        ])->assertCreated();

        $orderId = $response->json('data.id');
        $this->actingAs($user, 'api')->deleteJson("/orders/{$orderId}")->assertNoContent();

        $this->assertDatabaseMissing('cms.orders', ['id' => $orderId]);
        $this->assertSame(0, Inventory::where('order_id', $orderId)->count());
        $this->assertSame(0, $ammo->refresh()->inventory);
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $orderId = $this->actingAs($owner, 'api')->postJson('/orders', [
            'order_date' => '2026-07-12',
            'items' => [[
                'ammunition_id' => Ammunition::factory()->recycle($owner)->create()->id,
                'rounds' => 100,
                'cost' => 10,
            ]],
        ])->json('data.id');

        $this->actingAs(User::factory()->create(), 'api')
            ->getJson("/orders/{$orderId}")
            ->assertNotFound();
    }
}
