<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('pswd')->nullable();
            $table->string('profil')->default('avatar.jpg');
            $table->foreignId('pays')->nullable()->constrained('t_country');
            $table->foreignId('ville')->nullable()->constrained('t_city');
            $table->boolean('status')->default(true);
            $table->string('gender', 20)->nullable();
            $table->boolean('provider')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_users');
    }
};
