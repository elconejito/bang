<?php

namespace Tests\Feature;

use App\Models\Ammunition;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PurchaseInventoryDateMigrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_synchronizes_purchase_dates_without_changing_unlinked_inventory(): void
    {
        $user = User::factory()->create();
        $ammunition = Ammunition::factory()->recycle($user)->create();
        $order = Order::create([
            'order_date' => '2020-07-24',
            'rounds' => 100,
            'total_cost' => 55,
            'user_id' => $user->id,
        ]);
        $purchase = Inventory::factory()->recycle($user)->recycle($ammunition)->create([
            'order_id' => $order->id,
            'inventory_date' => '2020-07-25',
        ]);
        $adjustment = Inventory::factory()->recycle($user)->recycle($ammunition)->create([
            'order_id' => null,
            'inventory_date' => '2020-07-26',
        ]);

        $migration = require database_path('migrations/2026_07_13_224211_sync_purchase_inventory_dates_with_orders.php');
        $migration->up();

        $this->assertSame('2020-07-24', $purchase->refresh()->inventory_date->toDateString());
        $this->assertSame('2020-07-26', $adjustment->refresh()->inventory_date->toDateString());
    }
}
