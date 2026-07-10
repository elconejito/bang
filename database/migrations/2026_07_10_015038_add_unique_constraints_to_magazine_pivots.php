<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DELETE FROM cms.caliber_magazine a USING cms.caliber_magazine b WHERE a.ctid < b.ctid AND a.caliber_id = b.caliber_id AND a.magazine_id = b.magazine_id');
        DB::statement('DELETE FROM cms.firearm_magazine a USING cms.firearm_magazine b WHERE a.ctid < b.ctid AND a.firearm_id = b.firearm_id AND a.magazine_id = b.magazine_id');

        Schema::table('cms.caliber_magazine', function (Blueprint $table) {
            $table->unique(['caliber_id', 'magazine_id'], 'caliber_magazine_unique');
        });
        Schema::table('cms.firearm_magazine', function (Blueprint $table) {
            $table->unique(['firearm_id', 'magazine_id'], 'firearm_magazine_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.caliber_magazine', function (Blueprint $table) {
            $table->dropUnique('caliber_magazine_unique');
        });
        Schema::table('cms.firearm_magazine', function (Blueprint $table) {
            $table->dropUnique('firearm_magazine_unique');
        });
    }
};
