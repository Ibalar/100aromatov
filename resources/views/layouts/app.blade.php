<!DOCTYPE html>
<!--[if IE 8]>
<html class="ie" xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-RU" lang="ru-RU">
<![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-RU" lang="ru-RU">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name'))</title>
    <meta name="author" content="WebArt BY">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta name="description" content="@yield('meta_description', 'Интернет-магазин')">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/icon/icomoon/style.css') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/drift-basic.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/image-compare-viewer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.svg') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/logo/favicon.svg') }}">

    @stack('styles')
</head>
<body>

<!-- Scroll Top -->
<button id="goTop">
    <span class="border-progress"></span>
    <span class="ic-wrap">
            <span class="icon icon-CaretTopThin"></span>
        </span>
</button>
<!-- /Scroll Top -->

<!-- Preload -->
<div class="preload preload-container" id="preload">
    <div class="preload-logo">
        <div class="spinner"></div>
    </div>
</div>
<!-- /Preload -->



{{-- Main Content --}}
<main id="wrapper">
    {{-- Header --}}
    @include('partials.header')

    @yield('content')

    {{-- Footer --}}
    @include('partials.footer')
</main>

@include('partials.mobile-menu')

@include('partials.toolbar')

@include('partials.modals')


<!-- JS -->
<script src="{{ asset('assets/js/plugin/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/infinityslide.js') }}"></script>
<script src="{{ asset('assets/js/plugin/count-down.js') }}"></script>
<script src="{{ asset('assets/js/plugin/drift.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/wow.min.js') }}"></script>

<script src="{{ asset('assets/js/carousel.js') }}"></script>
<script src="{{ asset('assets/js/zoom.js') }}"></script>
<script src="{{ asset('assets/js/plugin/image-compare-viewer.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/image-compare-viewer.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/plugin/photoswipe-lightbox.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/photoswipe.umd.min.js') }}"></script>

@stack('scripts')
</body>
</html>
