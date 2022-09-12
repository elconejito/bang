<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdamTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idam.users', function (Blueprint $table) {
            $table->string('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('sub')->unique()->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('idam.password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('idam.password_resets');
        Schema::dropIfExists('idam.users');
    }
}
