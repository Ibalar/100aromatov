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

    <!-- Top Pick -->
    <section class="flat-spacing">
        <div class="container">
            <div class="sect-heading type-2 text-center wow fadeInUp">
                <h3 class="s-title">
                    Find Your Perfect Match
                </h3>
                <p class="s-desc text-body-1 cl-text-2">
                    From glow boosting serums to timeless beauty must haves start your routine today.
                </p>
            </div>
            <div dir="ltr" class="swiper tf-swiper wrap-sw-over" data-preview="4" data-tablet="3" data-mobile-sm="2"
                 data-mobile="2" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="2"
                 data-pagination-sm="2" data-pagination-md="3" data-pagination-lg="4">
                <div class="swiper-wrapper">
                    <!-- slide 1 -->
                    <div class="swiper-slide">
                        <div class="card-product wow fadeInUp">
                            <div class="card-product_wrapper">
                                <a href="product-detail.html" class="product-img">
                                    <img class="img-product" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-1.jpg" alt="Product">
                                    <img class="img-hover" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-1_2.jpg" alt="Product">
                                </a>
                                <ul class="product-action_list">
                                    <li class="wishlist">
                                        <a href="#;" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-heart"></span>
                                            <span class="tooltip">Add to Wishlist</span>
                                        </a>
                                    </li>
                                    <li class="compare">
                                        <a href="#compare" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-ArrowsLeftRight"></span>
                                            <span class="tooltip">Compare</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#quickView" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-Eye"></span>
                                            <span class="tooltip">Quick view</span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="product-badge_list">
                                    <li class="product-badge_item text-caption-01 new">NEW</li>
                                </ul>
                                <div class="product-action_bot">
                                    <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                       class="tf-btn btn-white small  w-100">
                                        Add to Cart

                                    </a>
                                </div>
                                <div class="product-marquee_sale">
                                    <div class="marquee-wrapper">
                                        <div class="initial-child-container">
                                            <!-- 1 -->
                                            <div class="marquee-child-item">
                                                HOT SALE 25% OFF
                                            </div>
                                            <i class="icon icon-Star2"></i>
                                            <!-- 2 -->
                                            <div class="marquee-child-item">
                                                HOT SALE 25% OFF
                                            </div>
                                            <i class="icon icon-Star2"></i>
                                            <!-- 3 -->
                                            <div class="marquee-child-item">
                                                HOT SALE 25% OFF
                                            </div>
                                            <i class="icon icon-Star2"></i>
                                            <!-- 4 -->
                                            <div class="marquee-child-item">
                                                HOT SALE 25% OFF
                                            </div>
                                            <i class="icon icon-Star2"></i>
                                            <!-- 5 -->
                                            <div class="marquee-child-item">
                                                HOT SALE 25% OFF
                                            </div>
                                            <i class="icon icon-Star2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-product_info">
                                <a href="product-detail.html"
                                   class="name-product lh-24 fw-medium link-underline-text">
                                    Pillow talk plump effect lip
                                </a>
                                <div class="star-wrap d-flex align-items-center">
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                </div>
                                <div class="price-wrap">
                                    <span class="price-new text-primary fw-semibold">$69,99</span>
                                    <span class="price-old text-caption-01 cl-text-3">$99,99</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- slide 2 -->
                    <div class="swiper-slide">
                        <div class="card-product wow fadeInUp">
                            <div class="card-product_wrapper">
                                <a href="product-detail.html" class="product-img">
                                    <img class="img-product" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-2.jpg" alt="Product">
                                    <img class="img-hover" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-2_2.jpg" alt="Product">
                                </a>
                                <ul class="product-action_list">
                                    <li class="wishlist">
                                        <a href="#;" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-heart"></span>
                                            <span class="tooltip">Add to Wishlist</span>
                                        </a>
                                    </li>
                                    <li class="compare">
                                        <a href="#compare" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-ArrowsLeftRight"></span>
                                            <span class="tooltip">Compare</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#quickView" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-Eye"></span>
                                            <span class="tooltip">Quick view</span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="product-badge_list">
                                    <li class="product-badge_item text-caption-01 sale">-25%</li>
                                </ul>
                                <div class="product-action_bot">
                                    <a href="#quickAdd" data-bs-toggle="modal"
                                       class="tf-btn btn-white small  w-100">
                                        Quick Add

                                    </a>
                                </div>
                            </div>
                            <div class="card-product_info">
                                <a href="product-detail.html"
                                   class="name-product lh-24 fw-medium link-underline-text">
                                    Origins
                                </a>
                                <div class="star-wrap d-flex align-items-center">
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                </div>
                                <div class="price-wrap">
                                    <span class="price-new text-primary fw-semibold">$29,99</span>
                                    <span class="price-old text-caption-01 cl-text-3">$49,99</span>
                                </div>
                                <ul class="product-color_list">
                                    <li class="product-color-item color-swatch hover-tooltip tooltip-bot active">
                                        <span class="tooltip color-filter">Brown</span>
                                        <span class="swatch-value bg-warm-brown"></span>
                                        <img src="assets/images/product/cosmetic/product-2.jpg"
                                             data-src="assets/images/product/cosmetic/product-2.jpg" alt="Image">
                                    </li>
                                    <li class="product-color-item color-swatch hover-tooltip tooltip-bot">
                                        <span class="tooltip color-filter">Beige</span>
                                        <span class="swatch-value bg-beige"></span>
                                        <img src="assets/images/product/cosmetic/product-2_3.jpg"
                                             data-src="assets/images/product/cosmetic/product-2_3.jpg" alt="Image">
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- slide 3 -->
                    <div class="swiper-slide">
                        <div class="card-product wow fadeInUp">
                            <div class="card-product_wrapper">
                                <a href="product-detail.html" class="product-img">
                                    <img class="img-product" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-3.jpg" alt="Product">
                                    <img class="img-hover" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-3.jpg" alt="Product">
                                </a>
                                <ul class="product-action_list">
                                    <li class="wishlist">
                                        <a href="#;" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-heart"></span>
                                            <span class="tooltip">Add to Wishlist</span>
                                        </a>
                                    </li>
                                    <li class="compare">
                                        <a href="#compare" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-ArrowsLeftRight"></span>
                                            <span class="tooltip">Compare</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#quickView" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-Eye"></span>
                                            <span class="tooltip">Quick view</span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="product-badge_list">
                                    <li class="product-badge_item text-caption-01 sale">-25%</li>
                                </ul>
                                <div class="product-action_bot">
                                    <a href="#quickAdd" data-bs-toggle="modal"
                                       class="tf-btn btn-white small  w-100">
                                        Quick Add

                                    </a>
                                </div>
                            </div>
                            <div class="card-product_info">
                                <a href="product-detail.html"
                                   class="name-product lh-24 fw-medium link-underline-text">
                                    Vanish Airbrush Pressed Powder
                                </a>
                                <div class="star-wrap d-flex align-items-center">
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                </div>
                                <div class="price-wrap">
                                    <span class="price-new text-primary fw-semibold">$15,99</span>
                                    <span class="price-old text-caption-01 cl-text-3">$25,99</span>
                                </div>
                                <ul class="product-color_list">
                                    <li class="product-color-item color-swatch hover-tooltip tooltip-bot active">
                                        <span class="tooltip color-filter">Brown</span>
                                        <span class="swatch-value bg-olive-brown"></span>
                                        <img src="assets/images/product/cosmetic/product-3.jpg"
                                             data-src="assets/images/product/cosmetic/product-3.jpg" alt="Image">
                                    </li>
                                    <li class="product-color-item color-swatch hover-tooltip tooltip-bot">
                                        <span class="tooltip color-filter">Blue</span>
                                        <span class="swatch-value bg-dark-blue"></span>
                                        <img src="assets/images/product/cosmetic/product-3_2.jpg"
                                             data-src="assets/images/product/cosmetic/product-3_2.jpg" alt="Image">
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- slide 4 -->
                    <div class="swiper-slide">
                        <div class="card-product wow fadeInUp">
                            <div class="card-product_wrapper">
                                <a href="product-detail.html" class="product-img">
                                    <img class="img-product" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-4.jpg" alt="Product">
                                    <img class="img-hover" loading="lazy" width="330" height="440"
                                         src="assets/images/product/cosmetic/product-4_2.jpg" alt="Product">
                                </a>
                                <ul class="product-action_list">
                                    <li class="wishlist">
                                        <a href="#;" class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-heart"></span>
                                            <span class="tooltip">Add to Wishlist</span>
                                        </a>
                                    </li>
                                    <li class="compare">
                                        <a href="#compare" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-ArrowsLeftRight"></span>
                                            <span class="tooltip">Compare</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#quickView" data-bs-toggle="offcanvas"
                                           class="hover-tooltip tooltip-left box-icon">
                                            <span class="icon icon-Eye"></span>
                                            <span class="tooltip">Quick view</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="product-action_bot">
                                    <a href="#shoppingCart" data-bs-toggle="offcanvas"
                                       class="tf-btn btn-white small  w-100">
                                        Add to cart

                                    </a>
                                </div>
                                <div class="product-countdown">
                                    <div class="js-countdown cd-has-zero" data-timer="1093120"
                                         data-labels="D : ,H : ,M : ,S">
                                    </div>
                                </div>
                            </div>
                            <div class="card-product_info">
                                <a href="product-detail.html"
                                   class="name-product lh-24 fw-medium link-underline-text">
                                    Supremya Baume
                                </a>
                                <div class="star-wrap d-flex align-items-center">
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                    <i class="icon icon-Star"></i>
                                </div>
                                <div class="price-wrap">
                                    <span class="price-new text-primary fw-semibold">$45,99</span>
                                    <span class="price-old text-caption-01 cl-text-3">$79,99</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </div>
    </section>
    <!-- /Top Pick -->

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
