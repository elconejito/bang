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
        DB::table('cms.magazines')->update([
            'status' => 'empty',
            'loaded_ammunition_id' => null,
            'loaded_rounds' => 0,
            'current_firearm_id' => null,
            'location_id' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing magazine state was explicitly disposable and cannot be reconstructed.
    }
};
