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
        Schema::create('t_sous_commande_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('commande_id')->constrained('t_sous_commande');
            $table->foreignUuid('produit_id')->constrained('t_produits');
            $table->float('price', 255,2);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sous_commande_detail');
    }
};
