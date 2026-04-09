<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'name_ru'], 'products_active_name_ru_idx');
            $table->index(['is_active', 'name_by'], 'products_active_name_by_idx');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->index(['is_active', 'name'], 'brands_active_name_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['is_active', 'sku'], 'product_variants_active_sku_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_name_ru_idx');
            $table->dropIndex('products_active_name_by_idx');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex('brands_active_name_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_active_sku_idx');
        });
    }
};
