<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart.items';

    private ?Collection $cachedItems = null;
    private ?array $cachedSummary = null;

    public function getRaw(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function add(int $variantId, int $qty = 1): void
    {
        $this->cachedItems = null;
        $this->cachedSummary = null;

        $qty = max(1, $qty);
        $variant = ProductVariant::query()
            ->whereKey($variantId)
            ->where('is_active', true)
            ->first();

        if (! $variant || (float) $variant->price_usd <= 0) {
            return;
        }

        $cart = $this->getRaw();
        $cart[$variantId] = ($cart[$variantId] ?? 0) + $qty;
        session([self::SESSION_KEY => $cart]);
    }

    public function setQty(int $variantId, int $qty): void
    {
        $this->cachedItems = null;
        $this->cachedSummary = null;

        $cart = $this->getRaw();
        $variant = ProductVariant::query()
            ->whereKey($variantId)
            ->where('is_active', true)
            ->first();

        if ($qty <= 0 || ! $variant || (float) $variant->price_usd <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = $qty;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(int $variantId): void
    {
        $this->cachedItems = null;
        $this->cachedSummary = null;

        $cart = $this->getRaw();
        unset($cart[$variantId]);
        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        $this->cachedItems = null;
        $this->cachedSummary = null;

        session()->forget(self::SESSION_KEY);
    }

    public function getItems(): Collection
    {
        if ($this->cachedItems !== null) {
            return $this->cachedItems;
        }

        $raw = $this->getRaw();
        if (empty($raw)) {
            return $this->cachedItems = collect();
        }

        $settings = Setting::getSettings();

        $variants = ProductVariant::query()
            ->whereIn('id', array_keys($raw))
            ->where('is_active', true)
            ->with([
                'product' => function ($q) {
                    $q->select('id', 'slug', 'name_ru', 'name_by');
                },
                'product.images' => function ($q) {
                    $q->select('id', 'product_id', 'path', 'alt_ru', 'alt_by', 'sort_order')
                        ->orderBy('sort_order');
                },
            ])
            ->get()
            ->keyBy('id');

        $items = collect();

        foreach ($raw as $variantId => $qty) {
            $variant = $variants->get((int) $variantId);
            if (! $variant || ! $variant->product) {
                continue;
            }

            if ((float) $variant->price_usd <= 0) {
                continue;
            }

            $qty = (int) $qty;
            if ($qty < 1) {
                continue;
            }

            $priceUsd = (float) $variant->final_price_usd;
            $priceByn = $settings->convertUsdToByn($priceUsd);
            $lineByn = round($priceByn * $qty, 2);

            $items->push([
                'variant_id' => (int) $variant->id,
                'product_id' => (int) $variant->product->id,
                'product_name' => localizedField($variant->product, 'name'),
                'product_slug' => $variant->product->slug,
                'sku' => $variant->sku,
                'volume_ml' => $variant->volume_ml,
                'qty' => $qty,
                'price_usd' => $priceUsd,
                'price_byn' => $priceByn,
                'line_byn' => $lineByn,
                'image' => $variant->product->images->first()?->path,
            ]);
        }

        return $this->cachedItems = $items;
    }

    public function getSummary(): array
    {
        if ($this->cachedSummary !== null) {
            return $this->cachedSummary;
        }

        $items = $this->getItems();
        $totalQty = $items->sum('qty');
        $totalByn = round((float) $items->sum('line_byn'), 2);

        return $this->cachedSummary = [
            'items' => $items,
            'total_qty' => $totalQty,
            'total_byn' => $totalByn,
        ];
    }

    public function getItemsForOrderPayload(): array
    {
        return $this->getItems()
            ->map(fn ($item) => [
                'variant_id' => $item['variant_id'],
                'qty' => $item['qty'],
            ])
            ->values()
            ->all();
    }
}
