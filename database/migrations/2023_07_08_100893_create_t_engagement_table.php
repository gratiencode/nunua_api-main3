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
        Schema::create('t_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('id_user')->constrained('t_users');
            $table->foreignUuid('id_entrep')->nullable()->constrained('t_entreprise');
            $table->foreignId('id_role')->constrained('t_roles');
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_engagement');
    }
};
