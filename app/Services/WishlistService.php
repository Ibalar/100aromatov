<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    private const SESSION_KEY = 'wishlist.product_ids';
    private const SESSION_SYNCED_KEY = 'wishlist.synced_with_customer';

    private ?array $cachedIds = null;
    private ?int $cachedCount = null;

    public function ids(): array
    {
        if ($this->cachedIds !== null) {
            return $this->cachedIds;
        }

        $customer = $this->authenticatedCustomer();

        if ($customer) {
            return $this->cachedIds = Wishlist::query()
                ->where('customer_id', $customer->id)
                ->orderBy('id')
                ->pluck('product_id')
                ->map(static fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray();
        }

        return $this->cachedIds = $this->sessionIds();
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    public function add(int $productId): void
    {
        $this->cachedIds = null;
        $this->cachedCount = null;

        $customer = $this->authenticatedCustomer();

        if ($customer) {
            Wishlist::query()->firstOrCreate([
                'customer_id' => $customer->id,
                'product_id' => $productId,
            ]);

            return;
        }

        $ids = $this->sessionIds();
        if (! in_array($productId, $ids, true)) {
            $ids[] = $productId;
            session([self::SESSION_KEY => $ids]);
        }
    }

    public function remove(int $productId): void
    {
        $this->cachedIds = null;
        $this->cachedCount = null;

        $customer = $this->authenticatedCustomer();

        if ($customer) {
            Wishlist::query()
                ->where('customer_id', $customer->id)
                ->where('product_id', $productId)
                ->delete();

            return;
        }

        $ids = array_values(array_filter($this->sessionIds(), fn (int $id) => $id !== $productId));
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
        $this->cachedIds = null;
        $this->cachedCount = null;

        $customer = $this->authenticatedCustomer();

        if ($customer) {
            Wishlist::query()
                ->where('customer_id', $customer->id)
                ->delete();

            return;
        }

        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        if ($this->cachedCount !== null) {
            return $this->cachedCount;
        }

        return $this->cachedCount = count($this->ids());
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

    public function syncSessionToCustomer(?Customer $customer = null): void
    {
        $this->cachedIds = null;
        $this->cachedCount = null;

        $customer ??= Auth::guard('customer')->user();
        if (! $customer) {
            return;
        }

        $sessionIds = $this->sessionIds();
        if ($sessionIds !== []) {
            $existingIds = Wishlist::query()
                ->where('customer_id', $customer->id)
                ->whereIn('product_id', $sessionIds)
                ->pluck('product_id')
                ->map(static fn ($id) => (int) $id)
                ->toArray();

            $existingMap = array_fill_keys($existingIds, true);
            $insertRows = [];

            foreach ($sessionIds as $productId) {
                if (! isset($existingMap[$productId])) {
                    $insertRows[] = [
                        'customer_id' => $customer->id,
                        'product_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if ($insertRows !== []) {
                Wishlist::query()->insert($insertRows);
            }
        }

        session()->forget(self::SESSION_KEY);
        session([self::SESSION_SYNCED_KEY => true]);
    }

    private function sessionIds(): array
    {
        return array_values(array_unique(array_map('intval', session(self::SESSION_KEY, []))));
    }

    private function authenticatedCustomer(): ?Customer
    {
        /** @var Customer|null $customer */
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            session()->forget(self::SESSION_SYNCED_KEY);

            return null;
        }

        if (! session(self::SESSION_SYNCED_KEY, false)) {
            $this->syncSessionToCustomer($customer);
        }

        return $customer;
    }
}
