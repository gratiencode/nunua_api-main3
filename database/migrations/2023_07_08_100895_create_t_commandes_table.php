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
        Schema::create('t_commandes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->string('order_number');
            $table->float('total', 255,2);
            $table->float('free_shipping',255,2);
            $table->float('grand_total', 255,2);
            $table->enum('status', ['pending', 'processing', 'completed', 'delivered'])->default('pending');
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_method', ['cash', 'credit_card', 'mobile_money'])->default('cash');
            $table->string('shipping_adresse');
            $table->string('shipping_city');
            $table->string('shipping_country');
            $table->string('shipping_code');
            $table->string('billing_fullname');
            $table->string('billing_phone');
            $table->string('billing_email');
            $table->string('billing_currency');
            $table->timestamps();
        });

        Schema::table('t_commandes', function ($table) {
            $table->foreign('user_id')->references('id')->on('t_users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
