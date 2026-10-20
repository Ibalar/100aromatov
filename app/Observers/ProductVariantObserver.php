<?php

namespace App\Observers;

use App\Models\Category;
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
                if ($category) {
                    $this->forgetCategoryPriceRange($category);

                    // Цена варианта влияет и на все родительские категории,
                    // т.к. их страницы включают товары потомков.
                    $parentId = $category->parent_id;
                    while ($parentId) {
                        $parent = Category::query()->find($parentId);
                        if (! $parent) {
                            break;
                        }
                        $this->forgetCategoryPriceRange($parent);
                        $parentId = $parent->parent_id;
                    }
                }

                // Миниатюрные категории показывают все варианты с объёмом <= 10 мл
                // вне зависимости от категории товара — инвалидируем их тоже.
                if ((float) $variant->volume_ml <= 10) {
                    foreach (Category::query()->where('is_miniature', true)->get(['id', 'parent_id']) as $miniCategory) {
                        $this->forgetCategoryPriceRange($miniCategory);
                    }
                }
            }
        }
    }

    private function forgetCategoryPriceRange(Category $category): void
    {
        // getDescendantIds() уже включает сам id категории.
        $categoryIds = array_unique($category->getDescendantIds());
        sort($categoryIds);

        Cache::forget('price_range_category_'.implode('_', $categoryIds));
        Cache::forget('price_range_category_mini_'.implode('_', $categoryIds));
    }
}
