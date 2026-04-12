<!-- Topbar -->
@php
    $headerPhones = collect($siteSettings->phones ?? [])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
@endphp
<div class="tf-topbar topbar-s3 bg-dark tf-btn-swiper-main">
    <div class="container-full">
        <div class="row align-items-center">
            <div class="col-lg-1 col-6 ">
                <div class="tf-list list-currenci">
                    <div class="tf-languages">
                        <select class="tf-dropdown-select style-default color-white type-languages"
                                onchange="if (this.value) { window.location.href = this.value; }">
                            <option value="{{ route('language.switch', 'ru') }}" @selected(app()->getLocale() === 'ru')>
                                Русский
                            </option>
                            <option value="{{ route('language.switch', 'by') }}" @selected(app()->getLocale() === 'by')>
                                Беларуская
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 d-none d-lg-block">
                <div class="tf-list justify-content-center">
                    @foreach($headerPhones as $phone)
                        <a href="{{ phoneHref($phone['number'] ?? null) }}" class="text-white link phone-a1 d-inline-flex align-items-center gap-2">
                            @if($iconUrl = settingPhoneIconUrl($phone['icon'] ?? null))
                                <img src="{{ $iconUrl }}"
                                     alt="{{ $phone['label'] ?? ($phone['number'] ?? 'Phone') }}"
                                     class="phone-a1__icon"
                                     style="height: 20px; max-width: 100%;">
                            @endif
                            <span>{{ $phone['number'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-1 col-6">
                <div class="d-flex align-items-center justify-content-end gap-20">
                    @if(filled($siteSettings->instagram_url ?? null))
                        <a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="d-flex">
                            <i class="fs-20 text-white link icon icon-InstagramLogo"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Topbar -->
<!-- Header -->
<header class="tf-header header-s7 scr-box-shadow">
    <div class="br-line fake-class bottom-0"></div>
    <div class="container-full">
        <div class="header-inner">
            <div class="box-open-menu-mobile d-xl-none">
                <a href="#mobileMenu" data-bs-toggle="offcanvas" class="btn-open-menu">
                    <i class="icon icon-List"></i>
                </a>
            </div>
            <div class="header-left d-none d-xl-flex">
                <a href="/" class="logo-site">
                    <img loading="lazy" width="130" height="35" src="{{ asset('assets/images/logo/logo.png') }}" alt="100aromatov.by">
                </a>
                <nav class="box-navigation">
                    <ul class="box-nav-menu">
                        <li class="menu-item position-relative">
                            <a href="/" class="item-link">
                                <span class="text cus-text">
                                    Главная
                                </span>
                            </a>
                        </li>

                        <li class="menu-item position-relative">
                            <a href="{{ route('brands.index') }}" class="item-link">
                                <span class="text cus-text">
                                    Бренды
                                </span>
                                <i class="icon icon-CaretDown"></i>
                            </a>

                            <div class="sub-menu mega-menu_home_v2 home-type_3">
                                @foreach($brandColumns as $column)
                                    <ul class="sub-menu_list">
                                        @foreach($column as $brand)
                                            <li>
                                                <a href="{{ route('brand.show', $brand->slug) }}"
                                                   class="sub-menu_link has-text">
                                                    <span class="cus-text">
                                                        {{ $brand->name }}
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach

                                        @if($loop->last)
                                            <li>
                                                <a href="{{ route('brands.index') }}"
                                                   class="sub-menu_link tf-btn-line-2 py-4 style-primary">
                                                    <span class="fw-semibold">
                                                        Смотреть все
                                                    </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                @endforeach
                            </div>
                        </li>

                        <li class="menu-item position-relative">
                            <a href="{{ route('categories.index') }}" class="item-link">
                                <span class="text cus-text">
                                    Парфюмерия
                                </span>
                                <i class="icon icon-CaretDown"></i>
                            </a>

                            <div class="sub-menu mega-menu_home_v2 home-type_3">
                                @foreach($categoryColumns as $column)
                                    <ul class="sub-menu_list">
                                        @foreach($column as $category)
                                            <li>
                                                <a href="{{ route('category.show', $category->slug) }}"
                                                   class="sub-menu_link has-text fw-semibold">
                                                    <span class="cus-text">
                                                        {{ localizedField($category, 'name') }}
                                                    </span>
                                                </a>
                                            </li>

                                            @foreach($category->children as $childCategory)
                                                <li>
                                                    <a href="{{ route('category.show', $childCategory->slug) }}"
                                                       class="sub-menu_link has-text">
                                                        <span class="cus-text">
                                                            {{ localizedField($childCategory, 'name') }}
                                                        </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endforeach

                                        @if($loop->last)
                                            <li>
                                                <a href="{{ route('categories.index') }}"
                                                   class="sub-menu_link tf-btn-line-2 py-4 style-primary">
                                                    <span class="fw-semibold">
                                                        Смотреть все
                                                    </span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                @endforeach
                            </div>
                        </li>

                        @foreach($menuPages ?? [] as $menuPage)
                            <li class="menu-item position-relative">
                                <a href="{{ route('pages.show', $menuPage->slug) }}" class="item-link">
                                    <span class="text cus-text">
                                        {{ localizedField($menuPage, 'name') }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            <div class="header-center d-xl-none">
                <a href="/" class="logo-site">
                    <img loading="lazy" width="150" height="40" src="{{ asset('assets/images/logo/logo.png') }}" alt="100aromatov.by">
                </a>
            </div>
            <div class="header-right">
                <form action="{{ route('search') }}" method="GET" class="form-search-nav style-3 d-none d-xl-block">
                    <fieldset>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Искать на сайте" required>
                    </fieldset>
                    <button type="submit" class="btn-action">
                        <i class="icon icon-MagnifyingGlass"></i>
                    </button>
                </form>
                <ul class="nav-icon-list">
                    <li class="d-none d-sm-block d-xl-none">
                        <a href="{{ route('search') }}" class="nav-icon-item link">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </a>
                    </li>
                    <li>
                        @if(auth('customer')->check())
                            <a href="{{ route('customer.account.dashboard') }}" class="nav-icon-item link">
                                <i class="icon icon-User"></i>
                            </a>
                        @else
                            <a href="{{ route('customer.login') }}" class="nav-icon-item link">
                                <i class="icon icon-User"></i>
                            </a>
                        @endif
                    </li>
                    <li class="d-none d-sm-block">
                        <a href="{{ route('wishlist.index') }}" class="nav-icon-item link js-wishlist-link">
                            <i class="icon {{ ($wishlistCount ?? 0) > 0 ? 'icon-heart' : 'icon-HeartStraight' }}"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#shoppingCart" data-bs-toggle="offcanvas" class="nav-icon-item link shop-cart">
                            <i class="icon icon-Handbag"></i>
                            <span class="count js-cart-count">{{ $cartCount ?? 0 }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
