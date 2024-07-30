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
        Schema::create('t_modification_user', function (Blueprint $table) {
            $table->bigInteger('id_modif_user', true);
            $table->bigInteger('id_user_mod')->nullable();
            $table->dateTime('date_update')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_modification_user');
    }
};
