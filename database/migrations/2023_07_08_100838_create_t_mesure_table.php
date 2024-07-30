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
        Schema::create('t_mesure', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->foreignUuid('id_entrep')->constrained('t_entreprise');
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
        Schema::dropIfExists('t_mesure');
    }
};
