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
        // Products table - additional indexes for catalog queries
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'created_at']);
        });

        // Product variants table - additional indexes for filtering
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['is_active', 'price_usd']);
            $table->index(['product_id', 'is_active', 'price_usd']);
        });

        // Product attribute values pivot table - index for attribute filtering
        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->index(['product_id', 'attribute_value_id'], 'pav_product_attr_value_idx');
        });

        // Categories table - indexes for tree operations
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['parent_id', 'is_active']);
            $table->index(['lft', 'rgt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'price_usd']);
            $table->dropIndex(['product_id', 'is_active', 'price_usd']);
        });

        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->dropIndex('pav_product_attr_value_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'is_active']);
            $table->dropIndex(['lft', 'rgt']);
        });
    }
};
