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
        Schema::create('t_panier', function (Blueprint $table) {
            $table->bigInteger('id_panier')->primary();
            $table->bigInteger('id_user_panier')->nullable()->index('id_user_panier');
            $table->bigInteger('id_produit_pandier')->nullable()->index('id_produit_pandier');
            $table->integer('qte')->nullable();
            $table->dateTime('date_add')->nullable()->useCurrent();
            $table->integer('status_panier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_panier');
    }
};
