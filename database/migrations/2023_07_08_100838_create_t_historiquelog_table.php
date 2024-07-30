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
        Schema::create('t_historiquelog', function (Blueprint $table) {
            $table->bigInteger('id_hist', true);
            $table->bigInteger('id_user_histo')->nullable()->index('id_user_histo');
            $table->date('dateLog')->nullable();
            $table->time('heure')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_historiquelog');
    }
};
