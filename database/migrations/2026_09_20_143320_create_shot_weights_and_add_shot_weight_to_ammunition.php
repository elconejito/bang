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
        Schema::create('reference.shot_weights', function (Blueprint $table) {
            $table->id();
            $table->string('label')->unique('shot_weights_label_unique');
            $table->timestamps();
        });

        DB::table('reference.shot_weights')->insert(
            collect([
                '3/8 oz', '1/2 oz', '5/8 oz', '11/16 oz', '3/4 oz', '7/8 oz', '15/16 oz',
                '1 oz', '1 1/8 oz', '1 1/4 oz', '1 3/8 oz', '1 1/2 oz', '1 5/8 oz',
                '1 3/4 oz', '1 7/8 oz', '2 oz', '2 1/4 oz',
            ])->map(fn (string $label): array => [
                'label' => $label,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all()
        );

        Schema::table('cms.ammunition', function (Blueprint $table) {
            $table->unsignedBigInteger('shot_weight_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.ammunition', function (Blueprint $table) {
            $table->dropColumn('shot_weight_id');
        });

        Schema::dropIfExists('reference.shot_weights');
    }
};
