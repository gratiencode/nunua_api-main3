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
        Schema::create('t_souscat', function (Blueprint $table) {
            $table->bigInteger('id_sous')->primary();
            $table->bigInteger('id_cat_sous')->nullable()->index('id_cat_sous');
            $table->string('name_sous')->nullable();
            $table->text('image_souscat')->nullable();
            $table->integer('status_sous')->nullable();
            $table->dateTime('date_add_sous')->nullable()->useCurrent();
            $table->integer('delete_souscat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_souscat');
    }
};
