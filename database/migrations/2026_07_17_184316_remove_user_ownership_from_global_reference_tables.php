<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private const TABLES = [
        'reference.ammunition_casings' => 'ammunition_casings_label_unique',
        'reference.ammunition_conditions' => 'ammunition_conditions_label_unique',
        'reference.bullet_types' => 'bullet_types_label_unique',
        'reference.caliber_types' => 'caliber_types_label_unique',
        'reference.location_types' => 'location_types_label_unique',
        'reference.primer_types' => 'primer_types_label_unique',
        'reference.shell_lengths' => 'shell_lengths_label_unique',
        'reference.shell_types' => 'shell_types_label_unique',
        'reference.shot_materials' => 'shot_materials_label_unique',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLES as $tableName => $indexName) {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropColumn('user_id');
                $table->unique('label', $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $tableName => $indexName) {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
                $table->integer('user_id')->nullable();
            });
        }
    }
};
