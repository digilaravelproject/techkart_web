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
        Schema::create('affiliate_wallet_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('affiliate_id');
            $table->string('transaction_number', 50)->unique();
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('payout_id')->nullable();
            $table->string('type', 20);
            $table->decimal('amount', 12, 4);
            $table->decimal('balance_after', 12, 4);
            $table->string('status', 20);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->cascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_wallet_transactions');
    }
};
