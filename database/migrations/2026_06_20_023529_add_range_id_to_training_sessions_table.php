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
        Schema::table('cms.training_sessions', function (Blueprint $table) {
            $table->foreignId('range_id')->nullable()->after('location_id')->constrained('cms.ranges')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.training_sessions', function (Blueprint $table) {
            $table->dropForeign(['range_id']);
            $table->dropColumn('range_id');
        });
    }
};
