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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('slug')->unique();

            $table->string('name_ru');
            $table->string('name_by');

            $table->longText('description_ru')->nullable();
            $table->longText('description_by')->nullable();

            $table->string('country')->nullable();

            $table->string('gender')->nullable()->index();
            $table->string('concentration')->nullable()->index();

            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();

            $table->unsignedInteger('views')->default(0);

            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_by')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_by')->nullable();

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['brand_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
