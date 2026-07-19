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
        Schema::table('cms.accessory_events', function (Blueprint $table) {
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->timestampTz('occurred_at')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->index(['subject_type', 'subject_id', 'occurred_at'], 'activity_events_subject_occurred_index');

            // Legacy accessory columns remain during the compatibility window, but
            // generalized events (such as a firearm lifecycle event) do not require them.
            $table->string('accessoryable_type')->nullable()->change();
            $table->unsignedBigInteger('accessoryable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.accessory_events', function (Blueprint $table) {
            $table->dropIndex('activity_events_subject_occurred_index');
            $table->dropColumn(['subject_type', 'subject_id', 'type', 'occurred_at', 'actor_id', 'metadata']);
            $table->string('accessoryable_type')->nullable(false)->change();
            $table->unsignedBigInteger('accessoryable_id')->nullable(false)->change();
        });
    }
};
