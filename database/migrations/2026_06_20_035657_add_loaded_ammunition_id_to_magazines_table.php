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
            $table->foreignId('loaded_ammunition_id')->nullable()->after('status')->constrained('cms.ammunition')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.magazines', function (Blueprint $table) {
            $table->dropForeign(['loaded_ammunition_id']);
            $table->dropColumn('loaded_ammunition_id');
        });
    }
};
