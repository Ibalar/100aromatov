@php
    $raw = request()->cookie('recently_viewed');
    $ids = [];

    if ($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $ids = array_map('intval', $decoded);
        }
    }

    $currentProductId = $product->id ?? null;
    $ids = array_values(array_diff($ids, [$currentProductId]));

    $recentlyViewed = collect();
    if (!empty($ids)) {
        $recentlyViewed = \App\Models\Product::query()
            ->active()
            ->whereIn('id', $ids)
            ->with([
                'brand:id,name,slug',
                'variants' => function ($query) {
                    $query->select('id', 'product_id', 'sku', 'volume_ml', 'price_usd', 'sale_price_usd', 'is_active')
                        ->where('is_active', true)
                        ->orderBy('price_usd');
                },
                'images' => function ($query) {
                    $query->select('id', 'product_id', 'path', 'sort_order')
                        ->orderBy('sort_order');
                },
            ])
            ->get()
            ->sortBy(function ($product) use ($ids) {
                return array_search($product->id, $ids);
            })
            ->take(6);
    }
@endphp

@if ($recentlyViewed->isNotEmpty())
<section class="flat-spacing">
    <div class="container">
        <div class="heading-section text-center mb-4">
            <h3>{{ __('Недавно просмотренные') }}</h3>
        </div>
        <div class="tf-grid-layout tf-col-6 tf-col-md-4 tf-col-lg-3 tf-col-xl-2">
            @foreach ($recentlyViewed as $rvProduct)
                @include('components.product-card', ['product' => $rvProduct])
            @endforeach
        </div>
    </div>
</section>
@endif
