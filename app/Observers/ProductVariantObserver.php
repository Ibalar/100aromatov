<?php

namespace App\Observers;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class ProductVariantObserver
{
    public function created(ProductVariant $variant): void
    {
        $this->updateProductPriceRange($variant);
        $this->invalidatePriceCaches($variant);
    }

    public function updated(ProductVariant $variant): void
    {
        if (
            $variant->wasChanged('price_usd')
            || $variant->wasChanged('sale_price_usd')
            || $variant->wasChanged('is_active')
        ) {
            $this->updateProductPriceRange($variant);
            $this->invalidatePriceCaches($variant);
        }
    }

    public function deleted(ProductVariant $variant): void
    {
        $this->updateProductPriceRange($variant);
        $this->invalidatePriceCaches($variant);
    }

    private function updateProductPriceRange(ProductVariant $variant): void
    {
        $product = $variant->product;
        if ($product) {
            $product->updatePriceRange();
        }
    }

    private function invalidatePriceCaches(ProductVariant $variant): void
    {
        Cache::forget('price_range_catalog');
        Cache::forget('price_range_sale');
        Cache::forget('home_featured_products');
        Cache::forget('home_sale_products');

        $product = $variant->product;
        if ($product) {
            Cache::forget("price_range_brand_{$product->brand_id}");

            if ($product->category_id) {
                $category = $product->category;
                $ids = collect([$product->category_id]);
                if ($category) {
                    $ids = $ids->merge(
                        $category->descendants()->pluck('id')
                    );
                }
                Cache::forget('price_range_category_'.$ids->sort()->implode('_'));
            }
        }
    }
}
