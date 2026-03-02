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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();
            $table->string('name')->index();

            $table->text('description_ru')->nullable();
            $table->text('description_by')->nullable();

            $table->string('country')->nullable();
            $table->string('logo')->nullable();

            $table->string('seo_title_ru')->nullable();
            $table->string('seo_title_by')->nullable();
            $table->text('seo_description_ru')->nullable();
            $table->text('seo_description_by')->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
