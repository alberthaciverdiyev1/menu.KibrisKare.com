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
        DB::statement('ALTER TABLE branches ALTER COLUMN weekly_hours TYPE jsonb USING weekly_hours::jsonb');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE branches ALTER COLUMN weekly_hours TYPE json USING weekly_hours::json');
    }
};
