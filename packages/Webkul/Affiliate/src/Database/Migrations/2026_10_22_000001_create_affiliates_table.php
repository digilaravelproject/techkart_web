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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->unique();
            $table->string('referral_code', 50)->unique();
            $table->string('status', 20)->default('active');
            $table->string('payout_method', 50)->nullable();
            $table->json('payout_details')->nullable();
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
