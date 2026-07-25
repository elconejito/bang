<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reference.colors', function (Blueprint $table) {
            $table->string('short_label', 20)->nullable()->after('label');
        });

        DB::table('reference.colors')->update(['short_label' => DB::raw('LEFT(label, 20)')]);

        Schema::table('reference.colors', function (Blueprint $table) {
            $table->string('short_label', 20)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('reference.colors', function (Blueprint $table) {
            $table->dropColumn('short_label');
        });
    }
};
