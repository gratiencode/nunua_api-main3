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
            $table->dropForeign(['id_mesure']);
            $table->dropColumn('id_mesure');
            $table->dropForeign(['id_marque']);
            $table->dropColumn('id_marque');
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');
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
