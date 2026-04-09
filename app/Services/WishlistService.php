<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class WishlistService
{
    private const SESSION_KEY = 'wishlist.product_ids';

    public function ids(): array
    {
        return array_values(array_unique(array_map('intval', session(self::SESSION_KEY, []))));
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function add(int $productId): void
    {
        $ids = $this->ids();
        if (! in_array($productId, $ids, true)) {
            $ids[] = $productId;
            session([self::SESSION_KEY => $ids]);
        }
    }

    public function remove(int $productId): void
    {
        $ids = array_values(array_filter($this->ids(), fn (int $id) => $id !== $productId));
        session([self::SESSION_KEY => $ids]);
    }

    public function toggle(int $productId): bool
    {
        if ($this->has($productId)) {
            $this->remove($productId);
            return false;
        }

        $this->add($productId);
        return true;
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return count($this->ids());
    }

    public function items(): Collection
    {
        $ids = $this->ids();
        if (empty($ids)) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'variants' => fn ($q) => $q->where('is_active', true)->orderBy('price_usd'),
            ])
            ->get();

        $indexed = $products->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $indexed->get($id))
            ->filter();
    }
}

