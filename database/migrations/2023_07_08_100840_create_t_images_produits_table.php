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
        Schema::create('t_images_produits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('id_produit');
            $table->string('images');
            $table->timestamps();
        });

        Schema::table('t_images_produits', function ($table) {
            $table->foreign('id_produit')->references('id')->on('t_produits')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_images_produits');
    }
};
