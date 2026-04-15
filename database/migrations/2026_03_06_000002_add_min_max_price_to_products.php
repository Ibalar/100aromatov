<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('min_price', 10, 2)->nullable()->after('category_id');
            $table->decimal('max_price', 10, 2)->nullable()->after('min_price');

            // Indexes for price filtering
            $table->index(['min_price', 'max_price']);
            $table->index(['is_active', 'min_price']);
            $table->index(['is_active', 'max_price']);
        });

        // Update existing products with min/max prices from their variants
        $this->updateProductPrices();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['min_price', 'max_price']);
            $table->dropIndex(['is_active', 'min_price']);
            $table->dropIndex(['is_active', 'max_price']);
            $table->dropColumn(['min_price', 'max_price']);
        });
    }

    /**
     * Update min/max prices for existing products based on their active variants.
     */
    private function updateProductPrices(): void
    {
        DB::table('products')
            ->select('id')
            ->orderBy('id')
            ->chunk(200, static function ($products): void {
                foreach ($products as $product) {
                    $priceRange = DB::table('product_variants')
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->selectRaw('MIN(price_usd) as min_price, MAX(price_usd) as max_price')
                        ->first();

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'min_price' => $priceRange?->min_price,
                            'max_price' => $priceRange?->max_price,
                        ]);
                }
            });
    }
};
