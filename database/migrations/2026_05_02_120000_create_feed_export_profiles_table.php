<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_export_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('platform', 32); // google|yandex
            $table->string('shop_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('currency', 8)->default('BYN');
            $table->string('language', 8)->default('ru');
            $table->text('include_category_slugs')->nullable();
            $table->text('exclude_category_slugs')->nullable();
            $table->text('include_brand_slugs')->nullable();
            $table->text('exclude_brand_slugs')->nullable();
            $table->boolean('only_in_stock')->default(true);
            $table->boolean('include_inactive_products')->default(false);
            $table->decimal('min_price_byn', 10, 2)->nullable();
            $table->decimal('max_price_byn', 10, 2)->nullable();
            $table->unsignedInteger('max_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_export_profiles');
    }
};
