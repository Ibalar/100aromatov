@php
    $wishlistMode = $wishlistMode ?? false;
@endphp

<div class="card-product has-size">
    <div class="card-product_wrapper square">
        @if($product->images->first())
            <a href="{{ route('product.show', $product->slug) }}" class="product-img">
                <img class="img-product" loading="lazy" width="330" height="330" src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
                @if($product->images->count() > 1)
                    <img class="img-hover" loading="lazy" width="330" height="330" src="{{ asset('storage/' . $product->images->skip(1)->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
                @endif
            </a>
        @endif

        <ul class="product-action_list">
            @unless($wishlistMode)
                <li class="wishlist">
                    <a href="#;" class="hover-tooltip tooltip-left box-icon js-wishlist-toggle {{ in_array($product->id, $wishlistIds ?? [], true) ? 'addwishlist' : '' }}" data-product-id="{{ $product->id }}">
                        <span class="icon icon-heart"></span>
                        <span class="tooltip">{{ __('В избранное') }}</span>
                    </a>
                </li>
            @endunless
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas" class="hover-tooltip tooltip-left box-icon js-quick-view-trigger" data-product-id="{{ $product->id }}">
                    <span class="icon icon-Eye"></span>
                    <span class="tooltip">{{ __('Быстрый просмотр') }}</span>
                </a>
            </li>
        </ul>

        @php
            $hasDiscount = false;
            $defaultVariant = $product->variants->first();

            $minRegularPrice = $product->variants->min('price_usd');
            $maxRegularPrice = $product->variants->max('price_usd');

            $minFinalPrice = $product->variants->min('final_price_usd');
            $maxFinalPrice = $product->variants->max('final_price_usd');

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

        @if($wishlistMode)
            <a href="#;" class="product-action_remove box-icon hover-tooltip tooltip-left js-wishlist-toggle addwishlist" data-product-id="{{ $product->id }}">
                <i class="icon icon-trash"></i>
                <span class="tooltip">{{ __('Удалить из избранного') }}</span>
            </a>
        @endif

        <div class="product-action_bot">
            <button type="button" class="tf-btn btn-white small w-100 js-add-to-cart" data-variant-id="{{ $defaultVariant?->id }}" data-qty="1">
                {{ __('Отложить') }}
            </button>
        </div>

        @php
            $volumes = $product->variants->pluck('volume_ml')->filter()->unique();
        @endphp

        @if($volumes->count())
            <div class="variant-box">
                <ul class="product-size_list">
                    @foreach($volumes as $volume)
                        <li class="size-item text-caption-01">
                            {{ $volume }} ml
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="card-product_info">
        <a href="{{ route('product.show', $product->slug) }}" class="name-product lh-24 fw-medium link-underline-text">
            {{ localizedField($product, 'name') }}
        </a>

        <div class="price-wrap">
            @if($minFinalPrice == $maxFinalPrice)
                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }}</span>

                @if($hasDiscount)
                    <span class="price-old text-caption-01 cl-text-3">{{ formatPriceByn($minRegularPrice) }}</span>
                @endif
            @else
                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }} &ndash; {{ formatPriceByn($maxFinalPrice) }}</span>
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
