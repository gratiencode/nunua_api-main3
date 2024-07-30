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
        Schema::create('t_mode_paie', function (Blueprint $table) {
            $table->integer('id_mode')->primary();
            $table->string('name_mode')->nullable();
            $table->text('image_mode')->nullable();
            $table->dateTime('date_add')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_mode_paie');
    }
};
