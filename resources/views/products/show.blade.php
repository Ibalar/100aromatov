@extends('layouts.app')

@php
    $seoTitle = trim((string) localizedField($product, 'seo_title'));
    $fallbackTitle = trim((string) localizedField($product, 'name'));
    $pageTitle = ($seoTitle !== '' ? $seoTitle : $fallbackTitle) . ' - ' . config('app.name');

    $seoDescription = trim((string) localizedField($product, 'seo_description'));
    $fallbackDescription = trim(preg_replace('/\s+/u', ' ', strip_tags((string) localizedField($product, 'description'))) ?? '');
    $metaDescription = $seoDescription !== ''
        ? $seoDescription
        : Str::limit($fallbackDescription, 160, '...');
    $metaImage = $product->images->first()?->path
        ? asset('storage/' . $product->images->first()->path)
        : asset('assets/images/logo/logo.png');
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('meta_image', $metaImage)

@push('schema_org')
    <x-schema-org
        type="product"
        :title="$pageTitle"
        :description="$metaDescription"
        :product="$product"
    />
@endpush

@section('content')

    @php
        $activeVariants = $product->variants->where('is_active', true);
        $defaultVariant = $activeVariants->first();
        $defaultVariantIsPreorder = $defaultVariant && (float) $defaultVariant->price_usd <= 0;
        $reviewsCount = $product->reviews->count();
        $averageRating = $reviewsCount > 0 ? round((float) $product->reviews->avg('rating'), 1) : null;
        $ratingDistribution = collect(range(5, 1))->mapWithKeys(function (int $rating) use ($product, $reviewsCount) {
            $count = $product->reviews->where('rating', $rating)->count();
            $percent = $reviewsCount > 0 ? (int) round(($count / $reviewsCount) * 100) : 0;

            return [$rating => [
                'count' => $count,
                'percent' => $percent,
            ]];
        });
    @endphp

    <x-breadcrumbs
        title=""
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            $product->category ? ['title' => localizedField($product->category, 'name'), 'url' => route('category.show', $product->category->slug)] : null,
            ['title' => localizedField($product, 'name')]
        ]"
    />

    <section class="section-product-single tf-main-product flat-spacing-3 pt-0">
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
                                                            src="{{ asset('storage/' . $image->path) }}"
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
                        <div class="tf-product-info-list">
                            <div class="tf-product-info-heading">

                                <h1 class="product-infor-name mb-12">{{ localizedField($product, 'name') }}</h1>

                                <div class="product-infor-meta mb-20">
                                    <div class="meta_rate">
                                        <div class="star-wrap normal d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star {{ $averageRating !== null && $i <= round($averageRating) ? 'is-active' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-caption-01 cl-text-2">({{ $reviewsCount }} {{ __('отзывов') }})</span>
                                    </div>
                                    <div class="br-line type-vertical"></div>
                                    <div class="meta_prd_code text-caption-01">
                                        <span class="cl-text-2">{{ __('Код товара:') }}</span>
                                        <span id="main-product-sku">{{ $defaultVariant?->sku ?: ('PRD-' . $product->id) }}</span>
                                    </div>
                                </div>



                                @if($product->brand)
                                    <p class="product-infor-desc cl-text-2 mb-8">
                                        <strong>{{ __('Бренд') }}:</strong>
                                        <a href="{{ route('brand.show', $product->brand->slug) }}" class="link">{{ $product->brand->name }}</a>
                                    </p>
                                @endif

                                <div class="product-infor-reality lh-24">
                                    <span class="text-caption-01">
                                        @if(localizedField($product, 'country'))
                                            <strong>{{ __('Страна') }}:</strong> {{ localizedField($product, 'country') }}
                                        @endif
                                        @if(localizedField($product, 'concentration'))
                                            @if(localizedField($product, 'country')) | @endif
                                            <strong>{{ __('По типу') }}:</strong> {{ localizedField($product, 'concentration') }}
                                        @endif
                                        @if(localizedField($product, 'gender'))
                                            @if(localizedField($product, 'country') || localizedField($product, 'concentration')) | @endif
                                            <strong>{{ __('Пол') }}:</strong> {{ localizedField($product, 'gender') }}
                                        @endif
                                    </span>
                                </div>
                                <p class="product-infor-desc cl-text-2 mb-20 mt-3">
                                    {{ __('Уважаемые покупатели. Информация о товаре предоставлена для ознакомления и не является публичной офертой. Для уточнения актуальной стоимости — свяжитесь с консультантом в магазине.') }}
                                </p>
                                <div class="product-infor-price">
                                    @if($defaultVariant)
                                        <h4 class="price-on-sale text-primary" id="main-product-price">
                                            {{ $defaultVariantIsPreorder ? __('Под заказ') : formatPriceByn($defaultVariant->final_price_usd) }}
                                        </h4>
                                        @if($defaultVariant->sale_price_usd && ! $defaultVariantIsPreorder)
                                            <div class="br-line type-vertical"></div>
                                            <p class="cl-text-3 text-decoration-line-through" id="main-product-sale-price">{{ formatPriceByn($defaultVariant->price_usd) }}</p>
                                        @else
                                            <p class="cl-text-3 text-decoration-line-through" id="main-product-sale-price" style="display:none;"></p>
                                        @endif
                                    @else
                                        <h4 class="price-on-sale">{{ __('Цена по запросу') }}</h4>
                                    @endif
                                </div>

                            </div>



                            <div class="br-line"></div>

                            <div class="tf-product-variant">
                                @include('components.product-variants', ['variants' => $product->variants, 'product' => $product])
                            </div>

                            <div class="tf-product-total-quantity mt-16">
                                <p id="product-qty-label" style="{{ $defaultVariantIsPreorder ? 'display:none;' : '' }}">{{ __('Количество') }}:</p>
                                <div class="group-action flex-wrap flex-xl-nowrap" id="product-order-actions" style="{{ $defaultVariantIsPreorder ? 'display:none;' : '' }}">
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
                            {{ __('Забронировать') }}
                                    </button>
                                    <button type="button" class="tf-btn type-xl btn-primary animate-btn w-100 js-buy-now mt-12">
                                        {{ __('Быстрая бронь') }}
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    class="tf-btn type-xl btn-primary animate-btn w-100 js-open-availability-modal"
                                    id="product-availability-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#productAvailabilityModal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ localizedField($product, 'name') }}"
                                    data-variant-id="{{ $defaultVariant?->id }}"
                                    data-variant-label="{{ $defaultVariant?->volume_ml ? $defaultVariant->volume_ml . ' ml' : '' }}"
                                    style="{{ $defaultVariantIsPreorder ? '' : 'display:none;' }}"
                                >
                                    {{ __('Уточнить наличие') }}
                                </button>

                            </div>

                            <div class="tf-product-extra-link">
                                <a href="#;" class="product-extra-icon link js-wishlist-toggle {{ in_array($product->id, $wishlistIds ?? [], true) ? 'addwishlist' : '' }}" data-product-id="{{ $product->id }}">
                                    <i class="icon icon-heart"></i>
                                    {{ __('В избранное') }}
                                </a>
                            </div>
                            <div class="br-line"></div>

                            <div class="tf-product-delivery-return">
                                <div class="product-delivery return">
                                    <i class="icon icon-Package"></i>
                                    <p>
                                        {{ __('Представленную на нашем сайте продукцию Вы можете приобрести в Торговом центре "НЕМИГА 3" ') }}
                                        <span class="fw-semibold">
                                                {{ __('(2 этаж, магазин 41 "100 Ароматов")') }}
                                            </span>
                                    {{-- {{ __('или заказать доставку товара по городу Минску') }} --}}
                                </p>
                            </div>
                            <div class="product-delivery">
                                <i class="icon icon-Timer"></i>
                                <p>
                                    {{ __('Доставка товара') }}:
                                    <span class="fw-semibold">
                                        {{ __('Доставка на данный момент не осуществляется') }}
                                        </span>
                                </p>
                            </div>

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
            <li class="nav-tab-item" role="presentation">
                <a href="#shipping-returns" data-bs-toggle="tab" class="tf-btn-tab" role="tab">
                    <span class="h5 fw-medium">{{ __('Доставка и оплата') }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active show" id="description" role="tabpanel">
                <div class="tab-content_desc">
                    @if($product->description_ru || $product->description_by)
                        <div class="box-desc">
                            <div class="desc_info cl-text">
                                {!! localizedField($product, 'description') !!}
                            </div>
                        </div>
                    @else
                        <p class="cl-text">{{ __('Описание отсутствует') }}</p>
                    @endif
                </div>
            </div>

            <div class="tab-pane" id="attributes" role="tabpanel">
                @include('components.product-attributes', ['attributeValues' => $product->attributeValues])
            </div>

            <div class="tab-pane" id="customer-reviews" role="tabpanel">
                <div class="product-desc_review">
                    <div class="box-rating mb-24">
                        <div class="rating-ratio">
                            <p class="text-display fw-medium">
                                {{ $averageRating !== null ? number_format($averageRating, 1) : '0.0' }}
                            </p>
                            <div class="star-wrap normal d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="icon icon-Star fs-24 {{ $averageRating !== null && $i <= round($averageRating) ? 'is-active' : '' }}"></i>
                                @endfor
                            </div>
                            <p class="rate-number">
                                ({{ $reviewsCount }} {{ __('оценок') }})
                            </p>
                        </div>

                        <div class="rating-progress-list">
                            @foreach($ratingDistribution as $rating => $distribution)
                                <div class="rate-progress-star fw-medium">
                                    <span class="number-star">{{ $rating }}</span>
                                    <i class="icon icon-Star fs-20 cl-text-yellow"></i>
                                    <div class="progress" role="progressbar" aria-valuenow="{{ $distribution['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $distribution['percent'] }}%;"></div>
                                    </div>
                                    <span class="number-percent">{{ $distribution['percent'] }}%</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="review-summary-actions">
                            <a href="#write-review-form" class="action btn-comment-review tf-btn animate-btn">
                                {{ __('Оставить отзыв') }}
                            </a>
                            <a href="{{ route('reviews.index') }}" class="action tf-btn btn-line ms-12">
                                {{ __('Все отзывы') }}
                            </a>
                        </div>
                    </div>

                    @if(session('review_success'))
                        <div class="alert alert-success mb-24">{{ session('review_success') }}</div>
                    @endif

                    <div class="box-comment cancel-review-wrap">
                        <div class="head">
                            <h4>{{ trans_choice('1 отзыв|:count отзыва|:count отзывов', $reviewsCount, ['count' => $reviewsCount]) }}</h4>
                        </div>

                        <div class="wg-comment">
                            <div class="comment-list">
                                @forelse($product->reviews as $review)
                                    <div class="box-comment">
                                        <div class="comment_info">
                                            <div class="info_image">
                                                <div class="review-avatar">
                                                    {{ mb_strtoupper(mb_substr($review->author_name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="info_author">
                                                <p class="h6 author__name">{{ $review->author_name }}</p>
                                                <p class="author_date text-caption-01 cl-text-3">
                                                    {{ $review->created_at->format('d.m.Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="comment-star-wrap mt-8 mb-8">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star {{ $i <= (int) $review->rating ? 'is-active' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="comment_text text-body-1">{{ $review->text }}</p>
                                        @if(filled($review->image))
                                            <div class="review-image-wrap">
                                                <a href="{{ asset('storage/' . $review->image) }}" target="_blank" rel="noopener noreferrer">
                                                    <img src="{{ asset('storage/' . $review->image) }}" alt="{{ __('Фото к отзыву') }}" class="review-image">
                                                </a>
                                            </div>
                                        @endif
                                        @if(filled($review->admin_reply))
                                            <div class="review-admin-reply">
                                                <p class="review-admin-reply__title">{{ __('Ответ администратора') }}</p>
                                                <p class="review-admin-reply__text">{{ $review->admin_reply }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="cl-text-2">{{ __('Пока нет отзывов. Будьте первым, кто оставит отзыв.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="box-write-comment write-review-wrap" id="write-review-form">
                        <div class="head">
                            <h5>{{ __('Оставить отзыв:') }}</h5>
                            <div class="review-form-subtitle">
                                {{ __('Отзыв появится на сайте после проверки администратором.') }}
                            </div>
                        </div>
                        <div class="review-rules mb-20">
                            <p class="mb-8">{{ __('reviews.rules_intro_short') }}</p>
                            <details class="review-rules__more">
                                <summary>{{ __('reviews.rules_read_full') }}</summary>
                                <ul class="review-rules__list mb-10 mt-10">
                                    <li>{{ __('reviews.rules_item_1') }}</li>
                                    <li>{{ __('reviews.rules_item_2') }}</li>
                                    <li>{{ __('reviews.rules_item_3') }}</li>
                                    <li>{{ __('reviews.rules_item_4') }}</li>
                                    <li>{{ __('reviews.rules_item_5') }}</li>
                                    <li>{{ __('reviews.rules_item_6') }}</li>
                                    <li>{{ __('reviews.rules_item_7') }}</li>
                                    <li>{{ __('reviews.rules_item_8') }}</li>
                                    <li>{{ __('reviews.rules_item_9') }}</li>
                                    <li>{{ __('reviews.rules_item_10') }}</li>
                                    <li>{{ __('reviews.rules_item_11') }}</li>
                                    <li>{{ __('reviews.rules_item_12') }}</li>
                                    <li>{{ __('reviews.rules_item_13') }}</li>
                                </ul>
                                <p class="mb-0">{{ __('reviews.rules_outro') }}</p>
                            </details>
                        </div>

                        @auth('customer')
                            @if($customerReview)
                                <div class="alert alert-info mb-16">
                                    @if($customerReview->is_approved)
                                        {{ __('Вы уже оставляли отзыв. Повторная отправка обновит его и снова отправит на модерацию.') }}
                                    @else
                                        {{ __('Ваш предыдущий отзыв ещё находится на модерации. Вы можете обновить его и отправить заново.') }}
                                    @endif
                                </div>
                            @endif

                            <form method="POST" action="{{ route('product.reviews.store', $product->slug) }}" class="form-rating" enctype="multipart/form-data">
                                @csrf
                                <div class="form-content mb-24">
                                    <div class="review-rating-select mb-20">
                                        <span class="tf-lable fw-medium">{{ __('Ваша оценка') }}</span>
                                        <div class="review-rating-stars">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input
                                                    type="radio"
                                                    id="review-rating-{{ $i }}"
                                                    name="rating"
                                                    value="{{ $i }}"
                                                    {{ (int) old('rating', $customerReview?->rating) === $i ? 'checked' : '' }}
                                                >
                                                <label for="review-rating-{{ $i }}" title="{{ $i }}">★</label>
                                            @endfor
                                        </div>
                                        @error('rating')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="tf-grid-layout md-col-2">
                                        <div class="tf-grid-layout">
                                            <fieldset class="tf-field">
                                                <label class="tf-lable fw-medium">{{ __('Ваше имя') }}</label>
                                                <input type="text" value="{{ auth('customer')->user()->full_name }}" readonly>
                                            </fieldset>
                                            <fieldset class="tf-field">
                                                <label class="tf-lable fw-medium">{{ __('Ваш Email') }}</label>
                                                <input type="email" value="{{ auth('customer')->user()->email }}" readonly>
                                            </fieldset>
                                        </div>

                                        <fieldset class="tf-field d-flex flex-column">
                                            <label for="review-text" class="tf-lable fw-medium">{{ __('Отзыв') }}</label>
                                            <textarea name="text" id="review-text" placeholder="{{ __('Поделитесь впечатлением о товаре') }}" class="h-md-100">{{ old('text', $customerReview?->text) }}</textarea>
                                            @error('text')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </fieldset>
                                    </div>

                                    <fieldset class="tf-field mt-20">
                                        <label for="review-image" class="tf-lable fw-medium">{{ __('Фото к отзыву (необязательно)') }}</label>
                                        <input type="file" name="image" id="review-image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                        <p class="cl-text-3 text-caption-01 mt-8 mb-0">{{ __('Форматы: JPG, PNG, WEBP. До 5 МБ.') }}</p>
                                        @error('image')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </fieldset>
                                </div>

                                <button type="submit" class="tf-btn animate-btn">
                                    {{ __('Отправить отзыв') }}
                                </button>
                            </form>
                        @else
                            <div class="review-login-note">
                                <p class="cl-text-2 mb-12">{{ __('Оставлять отзывы могут только авторизованные покупатели.') }}</p>
                                <div class="d-flex flex-wrap gap-12">
                                    <a href="{{ route('customer.login') }}" class="tf-btn animate-btn">{{ __('Войти') }}</a>
                                    <a href="{{ route('customer.register') }}" class="tf-btn btn-line">{{ __('Зарегистрироваться') }}</a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="shipping-returns" role="tabpanel">
                <div class="tab-content_desc desc-2 tf-grid-layout sm-col-1">
                    <div class="box-desc">
                        <h5 class="desc_title">{{ __('Внимание') }}</h5>
                        <div class="desc_info">
                            <p class="cl-text">
                                {{ __('Представленную на нашем сайте продукцию Вы можете приобрести в Торговом центре "НЕМИГА 3" (2 этаж, магазин 41 "100 Ароматов") или заказать доставку товара по городу Минску.') }}
                            </p>
                        </div>
                    </div>
                    <div class="box-desc">
                        <h5 class="desc_title">{{ __('Доставка товара') }}</h5>
                        <div class="desc_info">
                            <p class="cl-text">
                                <strong>{{ __('Доставка на данный момент не осуществляется') }}</strong>
                            </p>
                            {{--<p class="cl-text-2">
                                {{ __('В настоящее время доставка товара осуществляется только по городу Минску.') }}
                            </p>
                            <p class="cl-text-2">
                                {{ __('При заказе товара на сумму более 70 рублей - доставка бесплатная, при заказе на сумму менее 70 руб - стоимость доставки 5 руб.') }}
                            </p>
                            <p class="cl-text-2">
                                {{ __('Доставка за пределы МКАД (до 10 км) обсуждается индивидуально.') }}
                            </p>
                            <p class="cl-text-2">
                                {{ __('Срок доставки - 1-3 дня.') }}
                            </p>--}}
                            </div>
                        </div>
                        <div class="box-desc">
                            <h5 class="desc_title">{{ __('Оплата товара') }}</h5>
                            <p class="cl-text">
                                {{ __('Оплата за товар в магазине в ТЦ "Немига 3"') }}
                            </p>
                            <ul class="list">
                                <li class="cl-text">- {{ __('наличными денежными средствами') }}</li>
                                <li class="cl-text">- {{ __('банковской картой (терминал)') }}</li>
                                <li class="cl-text">- {{ __('картами рассрочки "Халва" (рассрочка 2 месяца) и "Картой покупок" Белгазпромбанка (рассрочка 2 месяца)') }}</li>
                                <li class="cl-text">- {{ __('по QR-коду (необходимо наличие интернет-банка на Вашем телефоне/планшете)') }}</li>
                                <li class="cl-text">- {{ __('через ЕРИП (выберите услугу "E-POS - оплата товаров и услуг и введите код 11952-1-1")') }}</li>
                                <li class="cl-text">- {{ __('платежный сервис "Оплати" (необходимо наличие на Вашем устройстве установленного приложения "Оплати")') }}</li>
                            </ul>
                        </div>
                    </div>
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

    .box-rating {
        display: grid;
        grid-template-columns: minmax(180px, 240px) 1fr auto;
        gap: 24px;
        align-items: start;
        padding: 24px;
        border: 1px solid #eee;
        border-radius: 16px;
    }

    .rating-ratio {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .rate-number {
        color: #777;
        margin-bottom: 0;
    }

    .rating-progress-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rate-progress-star {
        display: grid;
        grid-template-columns: 20px 24px 1fr 48px;
        gap: 10px;
        align-items: center;
    }

    .rate-progress-star .progress {
        height: 8px;
        background: #f0f0f0;
        border-radius: 999px;
    }

    .rate-progress-star .progress-bar {
        background: #181818;
        border-radius: 999px;
    }

    .review-summary-actions {
        display: flex;
        align-items: center;
    }

    .review-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #181818;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .write-review-wrap {
        margin-top: 24px;
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 24px;
    }

    .write-review-wrap .head {
        margin-bottom: 20px;
    }

    .review-form-subtitle {
        color: #777;
        margin-top: 6px;
    }

    .review-rules {
        padding: 14px 16px;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fafafa;
        font-size: 14px;
        line-height: 1.45;
    }

    .review-rules__list {
        padding-left: 18px;
        margin: 0;
    }

    .review-rules__list li + li {
        margin-top: 6px;
    }

    .review-rules__more summary {
        cursor: pointer;
        font-weight: 600;
        color: #181818;
        user-select: none;
    }

    .review-rating-select {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .review-rating-stars {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 4px;
    }

    .review-rating-stars input {
        display: none;
    }

    .review-rating-stars label {
        cursor: pointer;
        font-size: 28px;
        line-height: 1;
        color: #d0d0d0;
    }

    .review-rating-stars label:hover,
    .review-rating-stars label:hover ~ label,
    .review-rating-stars input:checked ~ label {
        color: #f5b301;
    }

    .review-login-note {
        border: 1px dashed #d9d9d9;
        border-radius: 16px;
        padding: 20px;
        background: #fafafa;
    }

    .review-image-wrap {
        margin-top: 14px;
    }

    .review-image {
        display: block;
        max-width: 220px;
        width: 100%;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .review-admin-reply {
        margin-top: 14px;
        padding: 14px 16px;
        border-left: 3px solid #181818;
        background: #f8f8f8;
        border-radius: 8px;
    }

    .review-admin-reply__title {
        margin-bottom: 6px;
        font-weight: 600;
    }

    .review-admin-reply__text {
        margin-bottom: 0;
    }

    .star-wrap .icon-Star,
    .comment-star-wrap .icon-Star {
        color: #d0d0d0;
    }

    .star-wrap .icon-Star.is-active,
    .comment-star-wrap .icon-Star.is-active {
        color: #f5b301;
    }

    @media (max-width: 991px) {
        .box-rating {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shouldOpenReviewTab = window.location.hash === '#customer-reviews'
            || @json($errors->has('rating') || $errors->has('text') || $errors->has('image') || session()->has('review_success'));

        if (!shouldOpenReviewTab) {
            return;
        }

        const trigger = document.querySelector('a[href="#customer-reviews"]');

        if (!trigger || typeof bootstrap === 'undefined' || !bootstrap.Tab) {
            return;
        }

        bootstrap.Tab.getOrCreateInstance(trigger).show();
    });
</script>
@endpush
