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
    <x-seo-meta />

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
    @stack('schema_org')

    @if(filled($siteSettings->metrics_head_code ?? null))
        {!! $siteSettings->metrics_head_code !!}
    @endif
</head>
<body>
@if(filled($siteSettings->metrics_body_start_code ?? null))
    {!! $siteSettings->metrics_body_start_code !!}
@endif
@php
    $privacyPage = collect($menuPages ?? [])->first(function ($page) {
        $slug = mb_strtolower((string) ($page->slug ?? ''));
        $name = mb_strtolower((string) localizedField($page, 'name'));

        return str_contains($slug, 'privacy')
            || str_contains($slug, 'policy')
            || str_contains($slug, 'confiden')
            || str_contains($slug, 'konfid')
            || str_contains($name, 'privacy')
            || str_contains($name, 'policy')
            || str_contains($name, 'конфиден')
            || str_contains($name, 'прыват');
    });

    $privacyPolicyUrl = $privacyPage ? route('pages.show', $privacyPage->slug) : null;
    $cookieConsentStatus = request()->cookie('cookie_consent_status');
@endphp

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

<div id="cookie-consent" class="cookie-consent" role="dialog" aria-live="polite" aria-label="{{ __('cookie.title') }}">
    <div class="cookie-consent__content">
        <p class="cookie-consent__text mb-0">
            @if($privacyPolicyUrl)
                {{ __('cookie.message') }}
                <a href="{{ $privacyPolicyUrl }}" class="cookie-consent__link">
                    {{ __('cookie.privacy_link_text') }}
                </a>.
            @else
                {{ __('cookie.message_without_link') }}
            @endif
        </p>
        <button type="button" class="tf-btn animate-btn cookie-consent__accept" data-cookie-consent-accept>
            {{ __('cookie.accept') }}
        </button>
        <button type="button" class="tf-btn btn-line cookie-consent__reject" data-cookie-consent-reject>
            {{ __('cookie.reject') }}
        </button>
    </div>
</div>


<!-- JS -->
<script src="{{ asset('assets/js/plugin/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/nouislider.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/infinityslide.js') }}"></script>
<script src="{{ asset('assets/js/plugin/count-down.js') }}"></script>
<script src="{{ asset('assets/js/plugin/drift.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/wow.min.js') }}"></script>
<script src="{{ asset('assets/js/carousel.js') }}"></script>
<script src="{{ asset('assets/js/plugin/image-compare-viewer.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/image-compare-viewer.js') }}"></script>
<script src="{{ asset('assets/js/plugin/photoswipe-lightbox.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/photoswipe.umd.min.js') }}"></script>
<script src="{{ asset('assets/js/zoom.js') }}"></script>
<script src="{{ asset('assets/js/quickview.js') }}"></script>

<script src="{{ asset('assets/js/shop.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/cart.js') }}"></script>
<script src="{{ asset('assets/js/wishlist.js') }}"></script>

<style>
    .cookie-consent {
        position: fixed;
        left: 16px;
        right: 16px;
        bottom: 16px;
        z-index: 9999;
    }

    .cookie-consent.cookie-consent--hidden {
        display: none;
    }

    .cookie-consent__content {
        max-width: 940px;
        margin: 0 auto;
        background: #111;
        color: #fff;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cookie-consent__text {
        flex: 1;
        line-height: 1.45;
    }

    .cookie-consent__link {
        color: #fff;
        text-decoration: underline;
    }

    .cookie-consent__accept {
        white-space: nowrap;
    }

    .cookie-consent__reject {
        white-space: nowrap;
    }

    @media (max-width: 767px) {
        .cookie-consent__content {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const CONSENT_KEY = 'cookie_consent_status_v1';
        const CONSENT_ACCEPTED = 'accepted';
        const CONSENT_REJECTED = 'rejected';
        const banner = document.getElementById('cookie-consent');
        const serverConsentStatus = @json($cookieConsentStatus);

        if (!banner) {
            return;
        }

        const savedConsent = localStorage.getItem(CONSENT_KEY);
        const effectiveConsent = savedConsent ?? serverConsentStatus;

        if (!savedConsent && (serverConsentStatus === CONSENT_ACCEPTED || serverConsentStatus === CONSENT_REJECTED)) {
            localStorage.setItem(CONSENT_KEY, serverConsentStatus);
        }

        if (effectiveConsent === CONSENT_ACCEPTED) {
            banner.classList.add('cookie-consent--hidden');
            document.cookie = 'cookie_consent_status=accepted; path=/; max-age=31536000; SameSite=Lax';
            return;
        }

        if (effectiveConsent === CONSENT_REJECTED) {
            banner.classList.add('cookie-consent--hidden');
            document.cookie = 'cookie_consent_status=rejected; path=/; max-age=31536000; SameSite=Lax';

            return;
        }

        const acceptButton = banner.querySelector('[data-cookie-consent-accept]');
        const rejectButton = banner.querySelector('[data-cookie-consent-reject]');

        if (!acceptButton || !rejectButton) {
            return;
        }

        acceptButton.addEventListener('click', function () {
            localStorage.setItem(CONSENT_KEY, CONSENT_ACCEPTED);
            document.cookie = 'cookie_consent_status=accepted; path=/; max-age=31536000; SameSite=Lax';
            banner.classList.add('cookie-consent--hidden');
        });

        rejectButton.addEventListener('click', function () {
            localStorage.setItem(CONSENT_KEY, CONSENT_REJECTED);
            document.cookie = 'cookie_consent_status=rejected; path=/; max-age=31536000; SameSite=Lax';
            banner.classList.add('cookie-consent--hidden');
        });
    });
</script>








@if(filled($siteSettings->metrics_body_end_code ?? null))
    {!! $siteSettings->metrics_body_end_code !!}
@endif
@stack('scripts')
</body>
</html>
