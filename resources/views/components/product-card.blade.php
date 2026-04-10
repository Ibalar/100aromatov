@php
    $wishlistMode = $wishlistMode ?? false;
    $defaultVariant = $product->variants->first();
    $variantsCount = $product->variants->count();
    $hasSingleVariant = $variantsCount === 1;
    $isSingleVariantPreorder = $hasSingleVariant && $defaultVariant && (float) $defaultVariant->price_usd <= 0;
    $pricedVariants = $product->variants->filter(static fn ($variant) => (float) $variant->price_usd > 0);
@endphp

<div class="card-product">
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
            @if($isSingleVariantPreorder)
                <button
                    type="button"
                    class="tf-btn btn-white small w-100 js-open-availability-modal"
                    data-bs-toggle="modal"
                    data-bs-target="#productAvailabilityModal"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ localizedField($product, 'name') }}"
                    data-variant-id="{{ $defaultVariant->id }}"
                    data-variant-label="{{ $defaultVariant->volume_ml ? $defaultVariant->volume_ml . ' ml' : '' }}"
                >
                    {{ __('Уточнить наличие') }}
                </button>
            @elseif($hasSingleVariant && $defaultVariant)
                <button type="button" class="tf-btn btn-white small w-100 js-add-to-cart" data-variant-id="{{ $defaultVariant->id }}" data-qty="1">
                    {{ __('Отложить') }}
                </button>
            @else
                <a href="{{ route('product.show', $product->slug) }}" class="tf-btn btn-white small w-100">
                    {{ __('Забронировать') }}
                </a>
            @endif
        </div>

    </div>

    <div class="card-product_info">
        <a href="{{ route('product.show', $product->slug) }}" class="name-product lh-24 fw-medium link-underline-text">
            {{ localizedField($product, 'name') }}
        </a>

        <div class="price-wrap">
            @if($hasSingleVariant && $isSingleVariantPreorder)
                <span class="price-new text-primary fw-semibold">{{ __('Под заказ') }}</span>
            @elseif($pricedVariants->isEmpty())
                <span class="price-new text-primary fw-semibold">{{ __('Под заказ') }}</span>
            @elseif($minFinalPrice == $maxFinalPrice)
                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }}</span>

                @if($hasDiscount)
                    <span class="price-old text-caption-01 cl-text-3">{{ formatPriceByn($minRegularPrice) }}</span>
                @endif
            @else
                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }} &ndash; {{ formatPriceByn($maxFinalPrice) }}</span>
            @endif
        </div>

    </div>
</div>
