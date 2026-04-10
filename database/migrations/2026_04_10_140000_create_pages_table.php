<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ru');
            $table->string('name_by');
            $table->longText('description_ru')->nullable();
            $table->longText('description_by')->nullable();
            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_by')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_by')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('show_in_menu')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
