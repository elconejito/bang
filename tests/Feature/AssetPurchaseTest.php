<?php

namespace Tests\Feature;

use App\Actions\Assets\SyncAssetPurchase;
use App\Models\Firearm;
use App\Models\Order;
use App\Models\User;
use App\Transformers\FirearmTransformer;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AssetPurchaseTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_links_an_asset_and_derives_its_purchase_fields(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $order = $this->order($user, 'R-100', '2026-01-02');
        $this->actingAs($user);

        app(SyncAssetPurchase::class)->execute($firearm, ['order_id' => $order->id, 'cost' => 725.50], $user->id);

        $data = (new FirearmTransformer)->transform($firearm->fresh());

        $this->assertSame($order->id, $data['purchase_order_id']);
        $this->assertSame('2026-01-02', $data['purchase_date']);
        $this->assertSame(725.50, $data['purchase_price']);
        $this->assertSame('R-100', $data['purchase_order']['order_ref']);
        $this->assertSame('725.50', $order->fresh()->total_cost);
    }

    public function test_it_moves_an_asset_between_orders_and_recalculates_both_totals(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $firstOrder = $this->order($user, 'FIRST', '2026-01-02');
        $secondOrder = $this->order($user, 'SECOND', '2026-02-02');
        $this->actingAs($user);
        $action = app(SyncAssetPurchase::class);

        $action->execute($firearm, ['order_id' => $firstOrder->id, 'cost' => 100], $user->id);
        $action->execute($firearm, ['order_id' => $secondOrder->id, 'cost' => 120], $user->id);

        $this->assertSame('0.00', $firstOrder->fresh()->total_cost);
        $this->assertSame('120.00', $secondOrder->fresh()->total_cost);
    }

    public function test_it_unlinks_an_asset_and_recalculates_its_order_total(): void
    {
        $user = User::factory()->create();
        $firearm = Firearm::factory()->recycle($user)->create();
        $order = $this->order($user, 'REMOVE', '2026-01-02');
        $this->actingAs($user);
        $action = app(SyncAssetPurchase::class);

        $action->execute($firearm, ['order_id' => $order->id, 'cost' => 100], $user->id);
        $action->execute($firearm, ['order_id' => null], $user->id);

        $this->assertDatabaseMissing('cms.order_assets', ['order_id' => $order->id, 'asset_id' => $firearm->id]);
        $this->assertSame('0.00', $order->fresh()->total_cost);
    }

    private function order(User $user, string $reference, string $date): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'order_ref' => $reference,
            'order_date' => $date,
            'rounds' => 0,
            'total_cost' => 0,
        ]);
    }
}
