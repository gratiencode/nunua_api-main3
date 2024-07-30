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
        Schema::table('t_entreprise', function (Blueprint $table) {
            $table->foreignId('ville')->after('description')->constrained('t_city');
            $table->foreignId('pays')->after('ville')->constrained('t_country');
            $table->string('num_impot')->after('pays')->nullable();
            $table->string('rccm_document')->after("rccm")->nullable();
            $table->string('import_document')->after("rccm_document")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_entreprise', function (Blueprint $table) {
            $table->dropColumn('ville');
            $table->dropColumn('pays');
            $table->dropColumn('num_impot');
            $table->dropColumn('rccm_document');
            $table->dropColumn('import_document');
        });
    }
};
