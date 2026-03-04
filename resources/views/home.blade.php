@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Slide Show -->
    <div class="tf-slideshow tf-btn-swiper-main hover-sw-nav">
        <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" data-effect="fade"
             data-delay="3000" data-auto="true" data-loop="true">
            <div class="swiper-wrapper">
                <!-- item 1 -->
                <div class="swiper-slide">
                    <div class="slideshow-wrap">
                        <div class="sld_image">
                            <img loading="lazy" width="1920" height="860" src="assets/images/slider/slider-16.jpg"
                                 alt="Image">
                        </div>
                        <div class="sld_content pst-2">
                            <div class="container">
                                <div class="content-sld_wrap text-center">
                                    <div class="heading">
                                        <p class="sub-text_sld text-body-1 cl-text-2 fade-item fade-item-1 mb-15">
                                            Where science meets nature for your finest skin.
                                        </p>
                                        <p class="title_sld text-display fw-medium fade-item fade-item-2">
                                            Reveal Your <br class="d-none d-sm-block">
                                            Timeless Beauty
                                        </p>
                                    </div>
                                    <div class="fade-item fade-item-3">
                                        <a href="shop-default.html" class="tf-btn btn-white">
                                            Shop Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- item 2 -->
                <div class="swiper-slide">
                    <div class="slideshow-wrap">
                        <div class="sld_image">
                            <img loading="lazy" width="1920" height="860" src="assets/images/slider/slider-17.jpg"
                                 alt="Image">
                        </div>
                        <div class="sld_content pst-2">
                            <div class="container">
                                <div class="content-sld_wrap text-center">
                                    <div class="heading">
                                        <p class="sub-text_sld text-body-1 cl-text-2 fade-item fade-item-1 mb-15">
                                            Skincare that reveals your timeless glow
                                        </p>
                                        <p class="title_sld text-display fw-medium fade-item fade-item-2">
                                            Where Science <br class="d-none d-sm-block">
                                            Meets Beauty
                                        </p>
                                    </div>
                                    <div class="fade-item fade-item-3">
                                        <a href="shop-default.html" class="tf-btn btn-white">
                                            Shop Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- item 3 -->
                <div class="swiper-slide">
                    <div class="slideshow-wrap">
                        <div class="sld_image">
                            <img loading="lazy" width="1920" height="860" src="assets/images/slider/slider-18.jpg"
                                 alt="Image">
                        </div>
                        <div class="sld_content pst-2">
                            <div class="container">
                                <div class="content-sld_wrap">
                                    <div class="heading">
                                        <p class="sub-text_sld text-body-1 cl-text-2 fade-item fade-item-1 mb-15">
                                            Cosmetics that enhance your confidence.
                                        </p>
                                        <p class="title_sld text-display fw-medium fade-item fade-item-2">
                                            Beauty That Empowers <br class="d-none d-sm-block">
                                            Every Moment
                                        </p>
                                    </div>
                                    <div class="fade-item fade-item-3">
                                        <a href="shop-default.html" class="tf-btn btn-white">
                                            Shop Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sw-line-default style-2 tf-sw-pagination"></div>
        </div>
    </div>
    <!-- /Slide Show -->
@endsection
