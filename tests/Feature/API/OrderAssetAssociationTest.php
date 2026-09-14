<?php

namespace Tests\Feature\API;

use App\Actions\Assets\SyncAssetPurchase;
use App\Enums\OrderAssetType;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderAssetAssociationTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @return array<string, array{string, string}> */
    public static function assetTypes(): array
    {
        return [
            'firearm' => ['firearm', 'firearms'],
            'suppressor' => ['suppressor', 'suppressors'],
            'optic' => ['optic', 'optics'],
            'light' => ['light', 'lights'],
            'misc' => ['misc-accessory', 'misc-accessories'],
            'mount' => ['mount', 'mounts'],
            'magazine' => ['magazine', 'magazines'],
        ];
    }

    #[DataProvider('assetTypes')]
    public function test_existing_asset_can_record_and_unlink_its_order(string $type, string $endpoint): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $class = OrderAssetType::models()[$type];
        $asset = $class::factory()->recycle($user)->create();
        $store = Store::factory()->recycle($user)->create();
        $order = Order::create(['user_id' => $user->id, 'store_id' => $store->id, 'order_date' => '2026-09-12', 'rounds' => 0, 'total_cost' => 0]);

        $this->putJson("/$endpoint/{$asset->id}", ['order_id' => $order->id, 'cost' => 125.50])
            ->assertOk()->assertJsonPath('data.purchase_order_id', $order->id)
            ->assertJsonPath('data.purchase_date', '2026-09-12')
            ->assertJsonPath('data.purchase_price', 125.5)
            ->assertJsonPath('data.purchase_store.id', $store->id);
        $this->getJson('/orders/'.$order->id)->assertOk()->assertJsonPath('data.total_cost', 125.5)->assertJsonCount(1, 'data.items');

        $this->putJson("/$endpoint/{$asset->id}", ['order_id' => null])
            ->assertOk()->assertJsonPath('data.purchase_order', null)->assertJsonPath('data.purchase_price', null);
        $this->getJson('/orders/'.$order->id)->assertOk()->assertJsonPath('data.total_cost', 0)->assertJsonCount(0, 'data.items');
        $this->assertModelExists($asset);
    }

    #[DataProvider('assetTypes')]
    public function test_asset_rejects_another_users_order_without_modifying_the_asset(string $type, string $endpoint): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'api');
        $class = OrderAssetType::models()[$type];
        $asset = $class::factory()->recycle($user)->create();
        $otherOrder = Order::create(['user_id' => $otherUser->id, 'order_date' => '2026-09-12', 'rounds' => 0, 'total_cost' => 0]);

        $this->putJson("/$endpoint/{$asset->id}", ['manufacturer' => 'Changed', 'order_id' => $otherOrder->id, 'cost' => 100])
            ->assertUnprocessable()->assertJsonValidationErrors('order_id');
        $this->assertSame($asset->manufacturer, $asset->fresh()->manufacturer);
        $this->assertNull($asset->fresh()->orderAsset);
    }

    #[DataProvider('assetTypes')]
    public function test_association_failure_rolls_back_the_asset_edit(string $type, string $endpoint): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $class = OrderAssetType::models()[$type];
        $asset = $class::factory()->recycle($user)->create();
        $order = Order::create(['user_id' => $user->id, 'order_date' => '2026-09-12', 'rounds' => 0, 'total_cost' => 0]);
        $this->mock(SyncAssetPurchase::class)->shouldReceive('execute')->once()
            ->andThrow(ValidationException::withMessages(['order_id' => 'The order is no longer available.']));

        $this->putJson("/$endpoint/{$asset->id}", ['manufacturer' => 'Changed', 'order_id' => $order->id, 'cost' => 100])
            ->assertUnprocessable();
        $this->assertSame($asset->manufacturer, $asset->fresh()->manufacturer);
    }
}
