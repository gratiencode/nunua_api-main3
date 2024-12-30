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
        Schema::table('t_produits', function (Blueprint $table) {
            $table->string('id_mesure')->nullable();
            $table->string('id_marque')->nullable();
            $table->string('id_category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_produits', function (Blueprint $table) {
            //
        });
    }
};
