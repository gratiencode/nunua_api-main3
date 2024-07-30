<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_commande_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('commande_id');
            $table->foreignUuid('produit_id');
            $table->float('price', 255,2);
            $table->integer('quantity');
            $table->timestamps();
        });
        
        Schema::table('t_commande_details', function ($table) {
            $table->foreign('commande_id')->references('id')->on('t_commandes')->onUpdate('cascade');
            $table->foreign('produit_id')->references('id')->on('t_produits')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
