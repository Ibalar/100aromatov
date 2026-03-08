<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     */
    public function created(ProductVariant $variant): void
    {
        $this->updateProductPriceRange($variant);
    }

    /**
     * Handle the ProductVariant "updated" event.
     */
    public function updated(ProductVariant $variant): void
    {
        // Update price range if price or active status changed
        if ($variant->isDirty('price_usd') || $variant->isDirty('is_active')) {
            $this->updateProductPriceRange($variant);
        }
    }

    /**
     * Handle the ProductVariant "deleted" event.
     */
    public function deleted(ProductVariant $variant): void
    {
        $this->updateProductPriceRange($variant);
    }

    /**
     * Update the product's min and max price range.
     */
    private function updateProductPriceRange(ProductVariant $variant): void
    {
        $product = $variant->product;
        if ($product) {
            $product->updatePriceRange();
        }
    }
}
