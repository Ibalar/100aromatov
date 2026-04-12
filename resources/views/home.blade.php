@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Banner Collection -->
    <section class="section-banner-cls">
        <div class="container-full">
            <div class="main-section">
                <div class="col-left">
                    <div class="banner-image-text type-abs style-14 h-100">
                        <a href="#" class="bn-image img-style radius-20">
                            <img loading="lazy" width="1170" height="794" src="{{ asset('assets/images/catalog/hero.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="bn-content">
                            <a href="#"
                               class="title text-display fw-medium link-underline-white text-decoration-thickness_3">
                                Широкий  <br>
                                выбор <br>
                                парфюмерии
                            </a>
                            <h6 class="desc text-body-1 letter-space--1">
                                Приходите к нам за покупками в ТЦ «Немига 3» <br class="d-none d-sm-block">
                                2 этаж, маг. 41.
                            </h6>
                            <a href="#" class="btn-action tf-btn">
                                В Каталог
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-right tf-grid-layout md-col-2 lg-col-1 gap-20">
                    <div class="box-image_v04 type-2">
                        <a href="#" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="{{ asset('assets/images/catalog/woman.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="#"
                               class="title h3 fw-medium link-underline-text text-decoration-thickness_3">
                                Для женщин
                            </a>
                            <a href="#" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        Перейти в каталог
                                    </span>
                            </a>
                        </div>
                    </div>
                    <div class="box-image_v04 type-2">
                        <a href="#" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="{{ asset('assets/images/catalog/men.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="#"
                               class="title h3 fw-medium text-white link-underline-text text-decoration-thickness_3">
                                Для мужчин
                            </a>
                            <a href="#" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        Подробнее
                                    </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Banner Collection -->
    <!-- Infinite Slide -->
    <div class="infiniteSlide-policy style-2 wow fadeInUp ">
        <div class="infiniteSlide infiniteSlide-wrapper" data-clone="3">
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                Весь наш товар - оригинальный
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                Постоянные акции и распродажи
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                Профессиональная консультация при выборе или покупке
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                Выбирайте свой любимый аромат и приходите к нам!
            </p>
        </div>
    </div>
    <!-- /Infinite Slide -->

    @if(($featuredProducts ?? collect())->isNotEmpty())
        <!-- Top Pick -->
        <section class="flat-spacing">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Популярные товары
                    </h3>
                </div>
                <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3" data-mobile-sm="2"
                     data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="2"
                     data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                    <div class="swiper-wrapper">
                        @foreach($featuredProducts as $product)
                            <div class="swiper-slide">
                                <div class="wow fadeInUp">
                                    <x-product-card :product="$product" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-dot-default tf-sw-pagination"></div>
                </div>
            </div>
        </section>
        <!-- /Top Pick -->
    @endif

    @if(($saleProducts ?? collect())->isNotEmpty())
        <section class="flat-spacing pt-0">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        Sale Products
                    </h3>
                    <p class="s-desc text-body-1 cl-text-2">
                        Products with active promotional prices.
                    </p>
                </div>

                <div class="tf-grid-layout lg-col-4 sm-col-2 grid-cls wow fadeInUp">
                    @foreach($saleProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Gallery -->
    <section class="flat-spacing">
        <div class="container">
            <div class="sect-heading text-center wow fadeInUp">
                <h3 class="s-title">
                    Shop Our Insta Glow
                </h3>
                <p class="s-desc">
                    Find clean beauty favorites loved by our online community.
                </p>
            </div>
            <div dir="ltr" class="swiper tf-swiper" data-preview="5" data-tablet="3" data-mobile-sm="3"
                 data-mobile="2" data-space="10" data-pagination="2" data-pagination-sm="3" data-pagination-md="4"
                 data-pagination-lg="5">
                <div class="swiper-wrapper">
                    <!-- slide 1 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="assets/images/gallery/gallery-27.jpg" alt="Image">
                            </div>
                            <a href="product-detail.html" class="box-icon hover-tooltip">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- slide 2 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="assets/images/gallery/gallery-28.jpg" alt="Image">
                            </div>
                            <a href="product-detail.html" class="box-icon hover-tooltip">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- slide 3 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="assets/images/gallery/gallery-29.jpg" alt="Image">
                            </div>
                            <a href="product-detail.html" class="box-icon hover-tooltip">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- slide 4 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="assets/images/gallery/gallery-30.jpg" alt="Image">
                            </div>
                            <a href="product-detail.html" class="box-icon hover-tooltip">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                    <!-- slide 5 -->
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="assets/images/gallery/gallery-31.jpg" alt="Image">
                            </div>
                            <a href="product-detail.html" class="box-icon hover-tooltip">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">View product</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </div>
    </section>
    <!-- /Gallery -->

@endsection
