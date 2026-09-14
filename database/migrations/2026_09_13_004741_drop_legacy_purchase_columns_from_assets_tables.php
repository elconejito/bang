<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['cms.firearms', 'cms.suppressors', 'cms.optics', 'cms.lights', 'cms.misc_accessories', 'cms.mounts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn(['purchase_date', 'purchase_price', 'purchase_store_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['cms.firearms', 'cms.suppressors', 'cms.optics', 'cms.lights', 'cms.misc_accessories', 'cms.mounts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 10, 2)->nullable();
                $table->unsignedBigInteger('purchase_store_id')->nullable();
            });
        }
    }
};
