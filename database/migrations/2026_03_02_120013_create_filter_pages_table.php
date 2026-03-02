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
        Schema::create('filter_pages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->json('filter_data');

            $table->string('h1_ru')->nullable();
            $table->string('h1_by')->nullable();

            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_by')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_by')->nullable();
            $table->longText('seo_text_ru')->nullable();
            $table->longText('seo_text_by')->nullable();

            $table->boolean('is_indexable')->default(true)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_pages');
    }
};
