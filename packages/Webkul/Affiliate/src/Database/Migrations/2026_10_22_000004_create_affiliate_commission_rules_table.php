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
        Schema::create('affiliate_commission_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entity_type', 20);
            $table->unsignedInteger('entity_id');
            $table->string('commission_type', 20)->default('percentage');
            $table->decimal('commission_value', 8, 2);
            $table->boolean('is_excluded')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id'], 'unique_entity_rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commission_rules');
    }
};
