<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->string('serial')->nullable()->after('model');
            $table->integer('location_id')->nullable()->after('serial');
            $table->date('purchase_date')->nullable()->after('location_id');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('purchase_date');
            $table->integer('purchase_store_id')->nullable()->after('purchase_price');
        });
    }

    public function down(): void
    {
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->dropColumn(['serial', 'location_id', 'purchase_date', 'purchase_price', 'purchase_store_id']);
        });
    }
};
