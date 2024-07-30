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
        Schema::create('t_affect_type_user', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('t_users');
            $table->foreignId('types_id')->constrained('t_types');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_affect_type_user');
    }
};
