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
        Schema::table('cms.suppressors', function (Blueprint $table) {
            $table->decimal('length', 6, 2)->nullable()->after('mount_type');
            $table->decimal('weight', 6, 2)->nullable()->after('length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.suppressors', function (Blueprint $table) {
            $table->dropColumn(['length', 'weight']);
        });
    }
};
