@php
    $defaultVariant = $product->variants->first();
    $variantsCount = $product->variants->count();
    $hasSingleVariant = $variantsCount === 1;
    $isSingleVariantPreorder = $hasSingleVariant && $defaultVariant && (float) $defaultVariant->price_usd <= 0;
    $description = \Illuminate\Support\Str::limit(strip_tags((string) localizedField($product, 'description')), 180);

    $pricedVariants = $product->variants->filter(static fn ($variant) => (float) $variant->price_usd > 0);
    $priceSource = $pricedVariants->isNotEmpty() ? $pricedVariants : $product->variants;
    $minRegularPrice = $priceSource->min('price_usd');
    $minFinalPrice = $priceSource->min('final_price_usd');
    $hasDiscount = $minFinalPrice < $minRegularPrice;
@endphp

<div class="card-product product-style_list">
    <div class="card-product_wrapper">
        @if($product->images->first())
            <a href="{{ route('product.show', $product->slug) }}" class="product-img">
                <img class="img-product" loading="lazy" width="330" height="440" src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
                @if($product->images->count() > 1)
                    <img class="img-hover" loading="lazy" width="330" height="440" src="{{ asset('storage/' . $product->images->skip(1)->first()->path) }}" alt="{{ localizedField($product, 'name') }}">
                @endif
            </a>
        @endif

        @if($product->is_featured || $hasDiscount)
            <ul class="product-badge_list">
                @if($product->is_featured)
                    <li class="product-badge_item text-caption-01 new">{{ __('NEW') }}</li>
                @endif
                @if($hasDiscount && $minRegularPrice > 0)
                    <li class="product-badge_item text-caption-01 sale">-{{ round((1 - $minFinalPrice / $minRegularPrice) * 100) }}%</li>
                @endif
            </ul>
        @endif
    </div>

    <div class="card-product_info">
        <a href="{{ route('product.show', $product->slug) }}" class="name-product lh-24 fw-medium link-underline-text">
            {{ localizedField($product, 'name') }}
        </a>

        <div class="price-wrap">
            @if($hasSingleVariant && $isSingleVariantPreorder)
                <span class="price-new text-primary fw-semibold">{{ __('Под заказ') }}</span>
            @elseif($pricedVariants->isNotEmpty() && $minRegularPrice !== null && $minFinalPrice !== null)
                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }}</span>
                @if($hasDiscount)
                    <span class="price-old text-caption-01 cl-text-3">{{ formatPriceByn($minRegularPrice) }}</span>
                @endif
            @else
                <span class="price-new text-primary fw-semibold">{{ __('Цена по запросу') }}</span>
            @endif
        </div>

        @if(!empty($description))
            <p class="description text-caption-01 mb-10">{{ $description }}</p>
        @endif

        @if($product->variants->isNotEmpty())
            <ul class="product-size_list mb-10">
                @foreach($product->variants->pluck('volume_ml')->filter()->unique()->take(4) as $volume)
                    <li class="size-item text-caption-01">{{ $volume }} ml</li>
                @endforeach
            </ul>
        @endif

        <ul class="product-action_list">
            <li>
                @if($isSingleVariantPreorder)
                    <a
                        href="#productAvailabilityModal"
                        data-bs-toggle="modal"
                        class="hover-tooltip box-icon js-open-availability-modal"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ localizedField($product, 'name') }}"
                        data-variant-id="{{ $defaultVariant->id }}"
                        data-variant-label="{{ $defaultVariant->volume_ml ? $defaultVariant->volume_ml . ' ml' : '' }}"
                    >
                        <span class="icon icon-Handbag"></span>
                        <span class="tooltip">{{ __('Уточнить наличие') }}</span>
                    </a>
                @elseif($hasSingleVariant && $defaultVariant)
                    <a href="#;" class="hover-tooltip box-icon js-add-to-cart" data-variant-id="{{ $defaultVariant->id }}" data-qty="1">
                        <span class="icon icon-Handbag"></span>
                        <span class="tooltip">{{ __('Отложить') }}</span>
                    </a>
                @else
                    <a href="{{ route('product.show', $product->slug) }}" class="hover-tooltip box-icon">
                        <span class="icon icon-Handbag"></span>
                        <span class="tooltip">{{ __('Забронировать') }}</span>
                    </a>
                @endif
            </li>
            <li class="wishlist">
                <a href="#;" class="hover-tooltip box-icon js-wishlist-toggle {{ in_array($product->id, $wishlistIds ?? [], true) ? 'addwishlist' : '' }}" data-product-id="{{ $product->id }}">
                    <span class="icon icon-heart"></span>
                    <span class="tooltip">{{ __('В избранное') }}</span>
                </a>
            </li>
            <li class="compare">
                <a href="#compare" data-bs-toggle="offcanvas" class="hover-tooltip box-icon">
                    <span class="icon icon-ArrowsLeftRight"></span>
                    <span class="tooltip">{{ __('Сравнить') }}</span>
                </a>
            </li>
            <li>
                <a href="#quickView" data-bs-toggle="offcanvas" class="hover-tooltip box-icon js-quick-view-trigger" data-product-id="{{ $product->id }}">
                    <span class="icon icon-Eye"></span>
                    <span class="tooltip">{{ __('Быстрый просмотр') }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
