@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Banner Collection -->
    <section class="section-banner-cls">
        <div class="container-full">
            <div class="main-section">
                <div class="col-left">
                    <div class="banner-image-text type-abs style-14 h-100">
                        <a href="shop-default.html" class="bn-image img-style radius-20">
                            <img loading="lazy" width="1170" height="794" src="assets/images/section/banner-15.jpg"
                                 alt="Image">
                        </a>
                        <div class="bn-content">
                            <a href="shop-default.html"
                               class="title text-display fw-medium text-white link-underline-white text-decoration-thickness_3">
                                Everything <br>
                                Your Pet <br>
                                Deserves
                            </a>
                            <h6 class="desc text-body-1 text-white letter-space--1">
                                Experience true wireless sound with deep bass, <br class="d-none d-sm-block">
                                crystal clarity.
                            </h6>
                            <a href="shop-default.html" class="btn-action tf-btn btn-white">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-right tf-grid-layout md-col-2 lg-col-1 gap-20">
                    <div class="box-image_v04 type-2">
                        <a href="shop-default.html" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="assets/images/collection/cls-18.jpg"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="shop-default.html"
                               class="title h3 fw-medium link-underline-text text-decoration-thickness_3">
                                Cat Feast
                            </a>
                            <p class="desc cl-text-2">
                                Healthy, tasty meals for cats
                            </p>
                            <a href="shop-default.html" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        Shop Now
                                    </span>
                            </a>
                        </div>
                    </div>
                    <div class="box-image_v04 type-2">
                        <a href="shop-default.html" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="assets/images/collection/cls-17.jpg"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="shop-default.html"
                               class="title h3 fw-medium link-underline-text text-decoration-thickness_3">
                                Pet Fashion
                            </a>
                            <p class="desc cl-text-2">
                                Cute outfits for every pet
                            </p>
                            <a href="shop-default.html" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        Shop Now
                                    </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Banner Collection -->
@endsection
