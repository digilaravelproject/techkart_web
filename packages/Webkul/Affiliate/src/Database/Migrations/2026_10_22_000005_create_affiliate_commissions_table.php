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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('order_item_id');
            $table->unsignedInteger('product_id');
            $table->decimal('item_price', 12, 4);
            $table->unsignedInteger('item_qty');
            $table->string('applied_tier', 20);
            $table->string('commission_type', 20);
            $table->decimal('commission_rate', 8, 2);
            $table->decimal('commission_amount', 12, 4);
            $table->string('status', 20)->default('pending');
            $table->timestamp('clears_at')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->cascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
