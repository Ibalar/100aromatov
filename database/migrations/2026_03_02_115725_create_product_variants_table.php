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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('sku')->unique();

            $table->integer('volume_ml')->nullable()->index();

            $table->decimal('price_usd', 10, 2);
            $table->decimal('sale_price_usd', 10, 2)->nullable();

            $table->boolean('is_tester')->default(false);
            $table->boolean('is_raspiv')->default(false);
            $table->boolean('is_unboxed')->default(false);
            $table->boolean('is_gift_wrapped')->default(false);

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
