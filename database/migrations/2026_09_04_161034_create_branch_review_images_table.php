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
        Schema::create('branch_review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_review_id')->constrained('branch_reviews')->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });

        if (Schema::hasColumn('branch_reviews', 'photos')) {
            Schema::table('branch_reviews', function (Blueprint $table) {
                $table->dropColumn('photos');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_review_images');

        if (!Schema::hasColumn('branch_reviews', 'photos')) {
            Schema::table('branch_reviews', function (Blueprint $table) {
                $table->json('photos')->nullable()->after('comment');
            });
        }
    }
};
