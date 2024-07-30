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
        Schema::create('t_affectation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_engagement')->constrained('t_engagement');
            $table->foreignUuid('id_permission')->constrained('t_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_affectation');
    }
};
