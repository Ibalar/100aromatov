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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->integer('lft')->default(0)->index();
            $table->integer('rgt')->default(0)->index();
            $table->integer('depth')->default(0);

            $table->string('slug')->unique();

            $table->string('name_ru');
            $table->string('name_by');

            $table->text('description_ru')->nullable();
            $table->text('description_by')->nullable();

            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_by')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_by')->nullable();
            $table->longText('seo_text_ru')->nullable();
            $table->longText('seo_text_by')->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
