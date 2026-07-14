<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE cms.inventories AS inventories
            SET inventory_date = orders.order_date
            FROM cms.orders AS orders
            WHERE orders.id = inventories.order_id
              AND inventories.inventory_date IS DISTINCT FROM orders.order_date
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
