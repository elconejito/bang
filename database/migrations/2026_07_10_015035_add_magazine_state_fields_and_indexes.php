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
        Schema::table('cms.magazines', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->constrained('cms.locations')->nullOnDelete();
            $table->foreignId('current_firearm_id')->nullable()->constrained('cms.firearms')->nullOnDelete();
            $table->unsignedInteger('loaded_rounds')->default(0);
            $table->index(['user_id', 'manufacturer', 'model_name', 'capacity'], 'magazines_group_lookup_index');
            $table->index(['user_id', 'location_id'], 'magazines_user_location_index');
            $table->index(['user_id', 'current_firearm_id'], 'magazines_user_current_firearm_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.magazines', function (Blueprint $table) {
            $table->dropIndex('magazines_group_lookup_index');
            $table->dropIndex('magazines_user_location_index');
            $table->dropIndex('magazines_user_current_firearm_index');
            $table->dropConstrainedForeignId('current_firearm_id');
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn('loaded_rounds');
        });
    }
};
