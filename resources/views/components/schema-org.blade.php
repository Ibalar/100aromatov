@props([
    'type' => null,
    'entity' => null,
    'title' => null,
    'description' => null,
    'products' => null,
    'brands' => null,
    'count' => null,
])

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
                        'price' => $product->min_price_usd,
                        'priceCurrency' => 'USD',
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
@endif
