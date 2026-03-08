<div class="card-product">
    <div class="card-product_wrapper">
        @if($product->images->first())
        <a href="{{ route('product.show', $product->slug) }}" class="product-img">
            <img src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $product->images->first()->alt_ru }}">
        </a>
        @endif

        <div class="product-action_bot">
            <a href="{{ route('product.show', $product->slug) }}" class="tf-btn btn-white small">
                Подробнее
            </a>
        </div>
    </div>
    <div class="card-product_info">
        <a href="{{ route('product.show', $product->slug) }}" class="name-product">
            {{ localizedField($product, 'name') }}
        </a>
        <div class="product-variants">
            @foreach($product->variants as $variant)
            <div class="variant-info">
                <span class="volume">{{ $variant->volume_ml }} ml</span>
                <span class="price">
                    @if($variant->sale_price_usd)
                    <span class="price-sale">${{ $variant->sale_price_usd }}</span>
                    @endif
                    <span class="price-current">${{ $variant->final_price_usd }}</span>
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
