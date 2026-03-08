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
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Brand",
    "name": "{{ $entity->name }}",
    "description": "{{ localizedField($entity, 'description') }}",
    "url": "{{ route('brand.show', $entity->slug) }}"
    @if($entity->logo)
    ,"logo": "{{ asset('storage/' . $entity->logo) }}"
    @endif
}
</script>
@elseif($type === 'brand_products')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "url": "{{ request()->url() }}",
    "numberOfItems": {{ $products->total() }},
    "itemListElement": [
        @foreach($products as $index => $product)
        @if($index > 0),@endif
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@type": "Product",
                "name": "{{ localizedField($product, 'name') }}",
                "url": "{{ route('product.show', $product->slug) }}",
                "brand": {
                    "@type": "Brand",
                    "name": "{{ $product->brand->name }}"
                },
                "offers": {
                    "@type": "Offer",
                    "price": "{{ $product->min_price_usd }}",
                    "priceCurrency": "USD",
                    "availability": "https://schema.org/InStock"
                }
            }
        }
        @endforeach
    ]
}
</script>
@elseif($type === 'brands_list')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "{{ $title }}",
    "description": "{{ $description }}",
    "url": "{{ request()->url() }}",
    "numberOfItems": {{ $count }},
    "itemListElement": [
        @foreach($brands as $index => $brand)
        @if($index > 0),@endif
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@type": "Brand",
                "name": "{{ $brand->name }}",
                "url": "{{ route('brand.show', $brand->slug) }}"
            }
        }
        @endforeach
    ]
}
</script>
@endif
