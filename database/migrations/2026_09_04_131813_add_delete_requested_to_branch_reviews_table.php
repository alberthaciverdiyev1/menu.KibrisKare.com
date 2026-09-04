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
        Schema::table('branch_reviews', function (Blueprint $table) {
            $table->boolean('delete_requested')->default(false)->after('is_approved');
            $table->text('delete_request_reason')->nullable()->after('delete_requested');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_reviews', function (Blueprint $table) {
            $table->dropColumn(['delete_requested', 'delete_request_reason']);
        });
    }
};
