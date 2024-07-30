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
        Schema::create('t_collection_has_category', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('id_collection')->constrained('t_collections');
            $table->foreignUuid('id_categorie')->constrained('t_categorie');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_collection_has_category');
    }
};
