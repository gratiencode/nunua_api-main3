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
        Schema::create('t_produits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_produit');
            $table->text('description');
            $table->float('price', 255, 2);
            $table->string('image');
            $table->foreignUuid('id_mesure')->constrained('t_mesure')->nullable();
            $table->foreignUuid('id_marque')->constrained('t_marque');
            $table->foreignUuid('id_category')->constrained('t_categorie');
            $table->foreignUuid('id_entrep')->constrained('t_entreprise');
            $table->timestamps();
            $table->boolean('etat_top')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('deleted')->default(false);
            $table->float('price_red', 255, 2);
            $table->integer('qte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_produits');
    }
};
