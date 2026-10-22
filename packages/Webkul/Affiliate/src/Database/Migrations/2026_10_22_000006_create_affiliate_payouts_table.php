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
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id');
            $table->string('payout_number', 50)->unique();
            $table->decimal('amount', 12, 4);
            $table->string('payout_method', 50);
            $table->json('payout_details');
            $table->string('status', 20)->default('requested');
            $table->string('transaction_reference', 100)->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
    }
};
