@props([
    'type' => null,
    'entity' => null,
    'title' => null,
    'description' => null,
    'products' => null,
    'brands' => null,
    'count' => null,
    'product' => null,
])

@php
    $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;
@endphp

@if($type === 'brand')
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Brand',
            'name' => $entity->name,
            'description' => localizedField($entity, 'description'),
            'url' => route('brand.show', $entity->slug),
        ];

        if($entity->logo) {
            $schema['logo'] = asset('storage/' . $entity->logo);
        }
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@elseif($type === 'brand_products')
    @php
        $items = [];
        foreach($products as $index => $product) {
            $pricedVariants = $product->variants->filter(static fn ($variant) => (float) $variant->price_usd > 0);
            $priceSource = $pricedVariants->isNotEmpty() ? $pricedVariants : $product->variants;
            $minPriceUsd = $priceSource->min('price_usd') ?? 0;
            $minPriceByn = round($minPriceUsd * $usdRate, 2);
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Product',
                    'name' => localizedField($product, 'name'),
                    'url' => route('product.show', $product->slug),
                    'brand' => [
                        '@type' => 'Brand',
                        'name' => $product->brand->name
                    ],
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $minPriceByn,
                        'priceCurrency' => 'BYN',
                        'availability' => 'https://schema.org/InStock'
                    ]
                ]
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => request()->url(),
            'numberOfItems' => $products->total(),
            'itemListElement' => $items
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@elseif($type === 'brands_list')
    @php
        $items = [];
        foreach($brands as $index => $brand) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Brand',
                    'name' => $brand->name,
                    'url' => route('brand.show', $brand->slug)
                ]
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => request()->url(),
            'numberOfItems' => $count,
            'itemListElement' => $items
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@elseif($type === 'product')
    @php
        $pricedVariants = $product->variants->filter(static fn ($variant) => (float) $variant->price_usd > 0);
        $priceSource = $pricedVariants->isNotEmpty() ? $pricedVariants : $product->variants;
        $minPriceUsd = $priceSource->min('final_price_usd') ?? 0;
        $maxPriceUsd = $priceSource->max('final_price_usd') ?? 0;
        $minPriceByn = round($minPriceUsd * $usdRate, 2);
        $maxPriceByn = round($maxPriceUsd * $usdRate, 2);
        $primaryImagePath = $product->images()
            ->orderBy('sort_order')
            ->value('path');
        $firstActiveVariant = $product->variants
            ->where('is_active', true)
            ->sortBy('price_usd')
            ->first();
        $sku = $firstActiveVariant?->sku;
        $approvedReviews = $product->reviews()
            ->where('is_approved', true)
            ->latest()
            ->take(3)
            ->get();
        $reviewCount = $product->reviews()->where('is_approved', true)->count();
        $averageRating = $reviewCount > 0
            ? round((float) $product->reviews()->where('is_approved', true)->avg('rating'), 1)
            : null;
        $availability = $firstActiveVariant && (float) $firstActiveVariant->price_usd <= 0
            ? 'https://schema.org/PreOrder'
            : 'https://schema.org/InStock';

        $reviewsSchema = $approvedReviews->map(static function ($review) {
            return [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $review->author_name,
                ],
                'datePublished' => optional($review->created_at)->toDateString(),
                'reviewBody' => $review->text,
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => (int) $review->rating,
                    'bestRating' => 5,
                    'worstRating' => 1,
                ],
            ];
        })->values()->all();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => localizedField($product, 'name'),
            'description' => $description,
            'image' => $primaryImagePath ? asset('storage/' . $primaryImagePath) : null,
            'url' => route('product.show', $product->slug),
            'sku' => $sku,
            'brand' => $product->brand ? [
                '@type' => 'Brand',
                'name' => $product->brand->name
            ] : null,
            'offers' => [
                '@type' => 'Offer',
                'price' => $minPriceByn,
                'lowPrice' => $minPriceByn,
                'highPrice' => $maxPriceByn,
                'priceCurrency' => 'BYN',
                'availability' => $availability,
                'url' => route('product.show', $product->slug),
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('app.name'),
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'BY',
                    ],
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'BY',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
                ],
            ],
            'aggregateRating' => $averageRating !== null ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $averageRating,
                'reviewCount' => $reviewCount,
                'bestRating' => 5,
                'worstRating' => 1,
            ] : null,
            'review' => $reviewsSchema !== [] ? $reviewsSchema : null,
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@elseif($type === 'category')
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => request()->url(),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
