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
        DB::table('cms.accessory_events')
            ->whereNull('subject_type')
            ->update([
                'subject_type' => DB::raw('accessoryable_type'),
                'subject_id' => DB::raw('accessoryable_id'),
                'type' => DB::raw('UPPER(event_type)'),
                'occurred_at' => DB::raw('event_date'),
                'actor_id' => DB::raw('user_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('cms.accessory_events')->update([
            'subject_type' => null,
            'subject_id' => null,
            'type' => null,
            'occurred_at' => null,
            'actor_id' => null,
            'metadata' => null,
        ]);
    }
};
