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
        DB::statement('UPDATE cms.magazines SET current_firearm_id = NULL WHERE id NOT IN (SELECT MIN(id) FROM cms.magazines WHERE current_firearm_id IS NOT NULL GROUP BY current_firearm_id) AND current_firearm_id IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX magazines_one_current_per_firearm_unique ON cms.magazines (current_firearm_id) WHERE current_firearm_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS cms.magazines_one_current_per_firearm_unique');
    }
};
