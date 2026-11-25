<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('channels')
            ->where('theme', 'testament')
            ->orWhereNull('theme')
            ->update(['theme' => 'default']);

        DB::table('theme_sections')
            ->where('theme_code', 'testament')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
