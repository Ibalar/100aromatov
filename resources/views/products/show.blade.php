@extends('layouts.app')

@section('title', localizedField($product, 'name') . ' - ' . config('app.name'))
@section('meta_description', localizedField($product, 'seo_description') ?: Str::limit(strip_tags(localizedField($product, 'description')), 160))

@push('schema_org')
    <x-schema-org
        type="product"
        :title="localizedField($product, 'name') . ' - ' . config('app.name')"
        :description="localizedField($product, 'seo_description') ?: localizedField($product, 'description')"
        :product="$product"
    />
@endpush

@section('content')

    @php
        $activeVariants = $product->variants->where('is_active', true);
        $defaultVariant = $activeVariants->first();
        $reviewsCount = $product->reviews->count();
    @endphp

    <x-breadcrumbs
        title=""
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            $product->category ? ['title' => localizedField($product->category, 'name'), 'url' => route('category.show', $product->category->slug)] : null,
            ['title' => localizedField($product, 'name')]
        ]"
    />

    <section class="section-product-single tf-main-product section-image-zoom flat-spacing-3 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="tf-product-media-wrap sticky-top">
                        @if($product->images->count() > 0)
                            <div class="product-thumbs-slider style-row row_left">
                                <div class="flat-wrap-media-product">
                                    <div dir="ltr" class="swiper tf-product-media-main" id="gallery-swiper-started" data-spacing="0">
                                        <div class="swiper-wrapper">
                                            @foreach($product->images as $image)
                                                <div class="swiper-slide">
                                                    <a href="{{ asset('storage/' . $image->path) }}" target="_blank" class="item" data-pswp-width="576px" data-pswp-height="768px">
                                                        <img
                                                            loading="lazy"
                                                            class="tf-image-zoom"
                                                            src="{{ asset('storage/' . $image->path) }}"
                                                            data-zoom="{{ asset('storage/' . $image->path) }}"
                                                            alt="{{ localizedField($image, 'alt') ?: localizedField($product, 'name') }}"
                                                        >
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @if($product->images->count() > 1)
                                    <div dir="ltr" class="swiper tf-product-media-thumbs other-image-zoom" data-direction="vertical" data-preview="7">
                                        <div class="swiper-wrapper stagger-wrap">
                                            @foreach($product->images as $image)
                                                <div class="swiper-slide stagger-item">
                                                    <div class="item">
                                                        <img
                                                            loading="lazy"
                                                            src="{{ asset('storage/' . $image->path) }}"
                                                            alt="{{ localizedField($image, 'alt') ?: localizedField($product, 'name') }}"
                                                        >
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="product-no-image d-flex align-items-center justify-content-center">
                                <span>{{ __('Изображение отсутствует') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="tf-product-info-wrap position-relative mt-md-0">
                        <div class="tf-zoom-main sticky-top"></div>
                        <div class="tf-product-info-list other-image-zoom">
                            <div class="tf-product-info-heading">
                                @if($product->category)
                                    <p class="product-infor-cate text-caption-01 mb-4">
                                        {{ localizedField($product->category, 'name') }}
                                    </p>
                                @endif

                                <h1 class="product-infor-name mb-12">{{ localizedField($product, 'name') }}</h1>

                                <div class="product-infor-meta mb-20">
                                    <div class="meta_rate">
                                        <div class="star-wrap normal d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star"></i>
                                            @endfor
                                        </div>
                                        <span class="text-caption-01 cl-text-2">({{ $reviewsCount }} {{ __('отзывов') }})</span>
                                    </div>
                                    <div class="br-line type-vertical"></div>
                                    <div class="meta_sold">
                                        <i class="icon icon-Eye text-primary"></i>
                                        <span class="text-caption-01 cl-text-2">{{ $product->views }} {{ trans_choice('просмотр|просмотра|просмотров', $product->views) }}</span>
                                    </div>
                                    <div class="br-line type-vertical"></div>
                                    <div class="meta_prd_code text-caption-01">
                                        <span class="cl-text-2">SKU:</span>
                                        <span id="main-product-sku">{{ $defaultVariant?->sku ?: ('PRD-' . $product->id) }}</span>
                                    </div>
                                </div>

                                <div class="product-infor-price mb-12">
                                    @if($defaultVariant)
                                        <h4 class="price-on-sale" id="main-product-price">{{ formatPriceByn($defaultVariant->final_price_usd) }}</h4>
                                        @if($defaultVariant->sale_price_usd)
                                            <div class="br-line type-vertical"></div>
                                            <p class="cl-text-3 text-decoration-line-through" id="main-product-sale-price">{{ formatPriceByn($defaultVariant->price_usd) }}</p>
                                        @else
                                            <p class="cl-text-3 text-decoration-line-through" id="main-product-sale-price" style="display:none;"></p>
                                        @endif
                                    @else
                                        <h4 class="price-on-sale">{{ __('Цена по запросу') }}</h4>
                                    @endif
                                </div>

                                @if($product->brand)
                                    <p class="product-infor-desc cl-text-2 mb-8">
                                        <strong>{{ __('Бренд') }}:</strong>
                                        <a href="{{ route('brand.show', $product->brand->slug) }}" class="link">{{ $product->brand->name }}</a>
                                    </p>
                                @endif

                                <div class="product-infor-reality lh-24">
                                    <span class="text-caption-01">
                                        @if($product->country)
                                            <strong>{{ __('Страна') }}:</strong> {{ $product->country }}
                                        @endif
                                        @if($product->concentration)
                                            @if($product->country) | @endif
                                            <strong>{{ __('Концентрация') }}:</strong> {{ $product->concentration }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="br-line"></div>

                            <div class="tf-product-variant">
                                @include('components.product-variants', ['variants' => $product->variants, 'product' => $product])
                            </div>

                            <div class="tf-product-total-quantity mt-16">
                                <p>{{ __('Количество') }}:</p>
                                <div class="group-action">
                                    <div class="wg-quantity">
                                        <button type="button" class="btn-quantity js-product-qty-minus">
                                            <i class="icon icon-minus"></i>
                                        </button>
                                        <input class="quantity-product js-product-qty" type="text" value="1" readonly>
                                        <button type="button" class="btn-quantity js-product-qty-plus">
                                            <i class="icon icon-plus"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn-action-price tf-btn type-xl animate-btn w-100 js-add-to-cart">
                                        {{ __('В корзину') }}
                                    </button>
                                </div>
                                <button type="button" class="tf-btn type-xl btn-primary animate-btn w-100 js-buy-now mt-12">
                                    {{ __('Купить сейчас') }}
                                </button>
                            </div>

                            <div class="tf-product-extra-link">
                                <a href="#;" class="product-extra-icon link js-wishlist-toggle {{ in_array($product->id, $wishlistIds ?? [], true) ? 'addwishlist' : '' }}" data-product-id="{{ $product->id }}">
                                    <i class="icon icon-heart"></i>
                                    {{ __('В избранное') }}
                                </a>
                                @if($product->gender)
                                    <span class="product-extra-icon">
                                        <i class="icon icon-User"></i>
                                        {{ __('Пол') }}:
                                        @if($product->gender === 'male')
                                            {{ __('Мужской') }}
                                        @elseif($product->gender === 'female')
                                            {{ __('Женский') }}
                                        @else
                                            {{ __('Унисекс') }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-product-description flat-spacing flat-animate-tab pt-0">
        <div class="container">
            <ul class="tab-btn-wrap-v1" role="tablist">
                <li class="nav-tab-item" role="presentation">
                    <a href="#description" data-bs-toggle="tab" class="tf-btn-tab active" role="tab">
                        <span class="h5 fw-medium">{{ __('Описание') }}</span>
                    </a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#attributes" data-bs-toggle="tab" class="tf-btn-tab" role="tab">
                        <span class="h5 fw-medium">{{ __('Характеристики') }}</span>
                    </a>
                </li>
                <li class="nav-tab-item" role="presentation">
                    <a href="#customer-reviews" data-bs-toggle="tab" class="tf-btn-tab" role="tab">
                        <span class="h5 fw-medium">{{ __('Отзывы') }}</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active show" id="description" role="tabpanel">
                    <div class="tab-content_desc">
                        @if($product->description_ru || $product->description_by)
                            <div class="box-desc">
                                <div class="desc_info cl-text-2">
                                    {!! localizedField($product, 'description') !!}
                                </div>
                            </div>
                        @else
                            <p class="cl-text-2">{{ __('Описание отсутствует') }}</p>
                        @endif
                    </div>
                </div>

                <div class="tab-pane" id="attributes" role="tabpanel">
                    @include('components.product-attributes', ['attributeValues' => $product->attributeValues])
                </div>

                <div class="tab-pane" id="customer-reviews" role="tabpanel">
                    @if($reviewsCount > 0)
                        <div class="product-desc_review">
                            <div class="box-comment">
                                @foreach($product->reviews as $review)
                                    <div class="box-review-item">
                                        <div class="comment-name">
                                            <span class="name fw-medium">{{ $review->user?->name ?? __('Пользователь') }}</span>
                                            <span class="text-caption-01 cl-text-3">{{ $review->created_at->format('d.m.Y') }}</span>
                                        </div>
                                        <div class="comment-star-wrap mt-8 mb-8">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star{{ $i <= $review->rating ? 'Fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="cl-text-2">{{ $review->text }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="cl-text-2">{{ __('Пока нет отзывов') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
<style>
    .product-no-image {
        min-height: 500px;
        border: 1px dashed #d9d9d9;
        border-radius: 12px;
        background: #fafafa;
        color: #8c8c8c;
    }

    .tf-product-extra-link {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
    }

    .product-extra-icon {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .product-desc_review .box-review-item + .box-review-item {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #eee;
    }
</style>
@endpush
