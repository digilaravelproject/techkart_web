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
        Schema::create('affiliate_wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id')->unique();
            $table->decimal('available_balance', 12, 4)->default(0.0000);
            $table->decimal('pending_balance', 12, 4)->default(0.0000);
            $table->decimal('locked_balance', 12, 4)->default(0.0000);
            $table->decimal('total_earned', 12, 4)->default(0.0000);
            $table->decimal('total_withdrawn', 12, 4)->default(0.0000);
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_wallets');
    }
};
