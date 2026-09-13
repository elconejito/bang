<?php

namespace Tests\Feature\API;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Mount;
use App\Models\Optic;
use App\Models\Store;
use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_mixed_order_asset_line_and_options(): void
    {
        $user = User::factory()->create();
        $ammo = Ammunition::factory()->recycle($user)->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $response = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-07-12', 'items' => [
            ['type' => 'ammunition', 'ammunition_id' => $ammo->id, 'rounds' => 10, 'cost' => 5],
            ['type' => 'firearm', 'asset_id' => $firearm->id, 'cost' => 100],
        ]])->assertCreated()->assertJsonPath('data.items_count', 2)->assertJsonPath('data.total_cost', 105);
        $orderId = $response->json('data.id');
        $this->assertDatabaseHas('cms.order_assets', ['order_id' => $orderId, 'asset_id' => $firearm->id]);
        $options = $this->actingAs($user, 'api')->getJson('/order-item-options?order_id='.$orderId)->assertOk()->json('data');
        $this->assertSame($orderId, collect($options)->firstWhere('type', 'firearm')['order_id']);
        $this->actingAs(User::factory()->create(), 'api')->getJson('/order-item-options?order_id='.$orderId)->assertNotFound();
    }

    public function test_malformed_and_duplicate_asset_lines_are_rejected(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $payload = ['order_date' => '2026-07-12', 'items' => [
            ['type' => 'firearm', 'asset_id' => $firearm->id], ['type' => 'firearm', 'asset_id' => $firearm->id],
        ]];
        $this->actingAs($user, 'api')->postJson('/orders', $payload)->assertUnprocessable();
        $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-07-12', 'items' => ['bad']])->assertUnprocessable();
    }

    public function test_order_supports_all_asset_types_and_preserves_assets_when_lines_removed(): void
    {
        $user = User::factory()->create();
        $models = ['firearm' => Firearm::factory(), 'suppressor' => Suppressor::factory(), 'optic' => Optic::factory(), 'light' => Light::factory(), 'misc-accessory' => MiscAccessory::factory(), 'mount' => Mount::factory(), 'magazine' => Magazine::factory()];
        $items = [];
        foreach ($models as $type => $factory) {
            $asset = $factory->recycle($user)->create();
            $items[] = ['type' => $type, 'asset_id' => $asset->id, 'cost' => 10];
        }
        $response = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-07-12', 'items' => $items])->assertCreated();
        $orderId = $response->json('data.id');
        $this->assertCount(7, $response->json('data.items'));
        $savedItems = collect($response->json('data.items'))->map(fn (array $item): array => [
            'id' => $item['id'], 'type' => $item['type'], 'asset_id' => $item['asset_id'], 'cost' => 20,
        ])->all();
        $this->putJson('/orders/'.$orderId, ['order_date' => '2026-07-13', 'items' => $savedItems])
            ->assertOk()->assertJsonPath('data.total_cost', 140)->assertJsonPath('data.rounds', 0);
        $this->actingAs($user, 'api')->putJson('/orders/'.$orderId, ['order_date' => '2026-07-12', 'items' => [$items[0]]])->assertOk()->assertJsonPath('data.items_count', 1);
        $this->assertDatabaseCount('cms.order_assets', 1);
        $this->assertDatabaseCount('cms.firearms', 1);
    }

    public function test_asset_line_id_must_belong_to_the_current_order(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $item = ['type' => 'firearm', 'asset_id' => $firearm->id, 'cost' => 10];
        $order = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-09-12', 'items' => [$item]])->assertCreated()->json('data');
        $this->putJson('/orders/'.$order['id'], ['order_date' => '2026-09-12', 'items' => [['id' => 999999, ...$item]]])
            ->assertUnprocessable()->assertJsonValidationErrors('items.0.id');
        $this->getJson('/orders/'.$order['id'])->assertOk()->assertJsonPath('data.total_cost', 10);
    }

    public function test_options_identify_ammunition_and_exclude_assets_linked_to_other_orders(): void
    {
        $user = User::factory()->create();
        $caliber = Caliber::factory()->recycle($user)->create(['label' => '9mm']);
        $ammo = Ammunition::factory()->recycle($user)->recycle($caliber)->create(['manufacturer' => 'Federal', 'label' => 'HST', 'weight' => 124]);
        $linked = Firearm::factory()->recycle($user)->create();
        $available = Firearm::factory()->recycle($user)->create();
        $order = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-09-12', 'items' => [['type' => 'firearm', 'asset_id' => $linked->id, 'cost' => 10]]])->assertCreated()->json('data');
        $options = collect($this->getJson('/order-item-options')->assertOk()->json('data'));
        $this->assertSame('9mm · Federal · HST · 124 gr', $options->firstWhere('type', 'ammunition')['label']);
        $firearms = $options->where('type', 'firearm');
        $this->assertFalse($firearms->contains('id', $linked->id));
        $this->assertNull($firearms->firstWhere('id', $available->id)['order_id']);
        $current = collect($this->getJson('/order-item-options?order_id='.$order['id'])->assertOk()->json('data'));
        $this->assertSame($order['id'], $current->where('type', 'firearm')->firstWhere('id', $linked->id)['order_id']);
        $this->assertNull($current->firstWhere('type', 'ammunition')['order_id']);
    }

    public function test_deleting_final_ammunition_line_keeps_order_containing_assets(): void
    {
        $user = User::factory()->create();
        $ammo = Ammunition::factory()->recycle($user)->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $order = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-09-12', 'items' => [
            ['ammunition_id' => $ammo->id, 'rounds' => 10, 'cost' => 5],
            ['type' => 'firearm', 'asset_id' => $firearm->id, 'cost' => 100],
        ]])->assertCreated()->json('data');
        $this->deleteJson('/inventories/'.$order['items'][0]['id'])->assertNoContent();
        $this->getJson('/orders/'.$order['id'])->assertOk()->assertJsonPath('data.rounds', 0)->assertJsonPath('data.total_cost', 100);
    }

    public function test_asset_cannot_be_added_to_another_order_or_by_another_user(): void
    {
        $owner = User::factory()->create();
        $asset = Firearm::factory()->recycle($owner)->create();
        $first = $this->actingAs($owner, 'api')->postJson('/orders', ['order_date' => '2026-07-12', 'items' => [['type' => 'firearm', 'asset_id' => $asset->id, 'cost' => 1]]])->assertCreated()->json('data.id');
        $this->actingAs($owner, 'api')->postJson('/orders', ['order_date' => '2026-07-13', 'items' => [['type' => 'firearm', 'asset_id' => $asset->id, 'cost' => 1]]])->assertUnprocessable();
        $this->actingAs(User::factory()->create(), 'api')->postJson('/orders', ['order_date' => '2026-07-13', 'items' => [['type' => 'firearm', 'asset_id' => $asset->id, 'cost' => 1]]])->assertUnprocessable();
        $this->assertDatabaseHas('cms.order_assets', ['order_id' => $first, 'asset_id' => $asset->id]);
    }

    public function test_destroying_order_detaches_inventory_but_keeps_stock_record(): void
    {
        $user = User::factory()->create();
        $ammo = Ammunition::factory()->recycle($user)->create();
        $id = $this->actingAs($user, 'api')->postJson('/orders', ['order_date' => '2026-07-12', 'items' => [['ammunition_id' => $ammo->id, 'rounds' => 10, 'cost' => 5]]])->json('data.id');
        $this->actingAs($user, 'api')->deleteJson('/orders/'.$id)->assertNoContent();
        $this->assertDatabaseHas('cms.inventories', ['ammunition_id' => $ammo->id, 'order_id' => null, 'rounds' => 10]);
    }

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

    public function test_deleting_an_order_detaches_lines_and_recalculates_inventory(): void
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
        $this->assertDatabaseHas('cms.inventories', ['ammunition_id' => $ammo->id, 'order_id' => null, 'rounds' => 100, 'cost' => 10]);
        $this->assertSame(100, $ammo->refresh()->inventory);
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
