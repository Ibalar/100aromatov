<div class="card-product">
    <div class="card-product_wrapper">
        @if($product->images->first())
        <a href="{{ route('product.show', $product->slug) }}" class="product-img">
            <img class="img-product" loading="lazy" width="330" height="440" src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
            @if($product->images->count() > 1)
            <img class="img-hover" loading="lazy" width="330" height="440" src="{{ asset('storage/' . $product->images->skip(1)->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
            @endif
        </a>
        @endif
        <ul class="product-action_list">
            <li class="wishlist">
                <a href="#;" class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-heart"></span>
                    <span class="tooltip">{{ __('В избранное') }}</span>
                </a>
            </li>
            <li class="compare">
                <a href="#compare" data-bs-toggle="offcanvas" class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-ArrowsLeftRight"></span>
                    <span class="tooltip">{{ __('Сравнить') }}</span>
                </a>
            </li>
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas" class="hover-tooltip tooltip-left box-icon">
                    <span class="icon icon-Eye"></span>
                    <span class="tooltip">{{ __('Быстрый просмотр') }}</span>
                </a>
            </li>
        </ul>
        @php
            $hasDiscount = false;
            $minRegularPrice = $product->variants->min('price_usd');
            $minFinalPrice = $product->variants->min('final_price_usd');
            $discountPercent = 0;
            if ($minFinalPrice < $minRegularPrice) {
                $hasDiscount = true;
                $discountPercent = round((1 - $minFinalPrice / $minRegularPrice) * 100);
            }
        @endphp
        @if($product->is_featured || $hasDiscount)
        <ul class="product-badge_list">
            @if($product->is_featured)
            <li class="product-badge_item text-caption-01 new">{{ __('NEW') }}</li>
            @endif
            @if($hasDiscount)
            <li class="product-badge_item text-caption-01 sale">-{{ $discountPercent }}%</li>
            @endif
        </ul>
        @endif
        <div class="product-action_bot">
            <a href="#quickAdd" data-bs-toggle="modal" class="tf-btn btn-white small w-100">
                {{ __('Быстрый заказ') }}
            </a>
        </div>
    </div>
    <div class="card-product_info">
        <a href="{{ route('product.show', $product->slug) }}" class="name-product lh-24 fw-medium link-underline-text">
            {{ localizedField($product, 'name') }}
        </a>
        <div class="star-wrap d-flex align-items-center">
            @php
                $avgRating = $product->reviews->avg('rating') ?? 0;
            @endphp
            @for($i = 1; $i <= 5; $i++)
                <i class="icon icon-Star {{ $i <= round($avgRating) ? '' : 'text-caption-03' }}"></i>
            @endfor
        </div>
        <div class="price-wrap">
            <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }}</span>
            @if($hasDiscount)
            <span class="price-old text-caption-01 cl-text-3">{{ formatPriceByn($minRegularPrice) }}</span>
            @endif
        </div>
        <div class="product-variants">
            @foreach($product->variants as $variant)
            <div class="variant-info">
                <span class="volume">{{ $variant->volume_ml }} ml</span>
                @if($variant->is_tester) <span class="badge-tester">{{ __('Тестер') }}</span> @endif
                @if($variant->is_raspiv) <span class="badge-raspiv">{{ __('Распив') }}</span> @endif
                @if($variant->is_unboxed) <span class="badge-unboxed">{{ __('Уценка') }}</span> @endif
                @if($variant->is_exclusive) <span class="badge-exclusive">{{ __('Отливант') }}</span> @endif
                <span class="price">
                    @if($variant->sale_price_usd)
                    <span class="price-sale">{{ formatPriceByn($variant->sale_price_usd) }}</span>
                    @endif
                    <span class="price-current">{{ formatPriceByn($variant->final_price_usd) }}</span>
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
