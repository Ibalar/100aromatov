<!-- Topbar -->
<div class="tf-topbar topbar-s3 bg-dark tf-btn-swiper-main">
    <div class="container-full">
        <div class="row align-items-center">
            <div class="col-lg-1 col-6 ">
                <div class="tf-list list-currenci">
                    <div class="tf-languages">
                        <select class="tf-dropdown-select style-default color-white type-languages">
                            <option>Русский</option>
                            <option>Беларускi</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-10 d-none d-lg-block">
                <div class="tf-list justify-content-center">
                    <a href="tel:+375291519223" class="text-white link phone-a1 d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('assets/icon/a1.svg') }}"
                             alt="A1"
                             class="phone-a1__icon"
                             width="20"
                             height="20">
                        +375 29 151 92 23
                    </a>
                    <a href="tel:+375295577883" class="text-white link phone-a1 d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('assets/icon/mts.webp') }}"
                             alt="MTS"
                             class="phone-a1__icon"
                             width="20"
                             height="20">
                        +375 29 55 77 88 3
                    </a>
                    <a href="tel:+375257274050" class="text-white link phone-a1 d-inline-flex align-items-center gap-2">
                        <img src="{{ asset('assets/icon/life.png') }}"
                             alt="Life"
                             class="phone-a1__icon"
                             width="40"
                             height="20">
                        +375 25 727 40 50
                    </a>
                </div>
            </div>
            <div class="col-lg-1 col-6">
                <div class="d-flex align-items-center justify-content-end gap-20">

                    <a href="https://www.instagram.com/100aromatov.by/" target="_blank" class="d-flex"><i
                            class="fs-20 text-white link icon icon-InstagramLogo"></i></a>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Topbar -->
<!-- Header -->
<header class="tf-header header-s7 scr-box-shadow">
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

                        <li class="menu-item">
                            <a href="#" class="item-link">
                                        <span class="text cus-text">
                                            Парфюмерия
                                        </span>
                                <i class="icon icon-CaretDown"></i>
                            </a>
                            <div class="sub-menu mega-menu">
                                <div class="container-full">
                                    <div class="row">

                                    </div>
                                </div>
                            </div>
                        </li>



                    </ul>
                </nav>
            </div>
            <div class="header-center d-xl-none">
                <a href="/" class="logo-site">
                    <img loading="lazy" width="150" height="40" src="{{ asset('assets/images/logo/logo.png') }}" alt="100aromatov.by">
                </a>
            </div>
            <div class="header-right">
                <form action="search-result.html" class="form-search-nav style-3 d-none d-xl-block">
                    <fieldset>
                        <input type="text" placeholder="Искать на сайте" required="">
                    </fieldset>
                    <button type="submit" class="btn-action">
                        <i class="icon icon-MagnifyingGlass"></i>
                    </button>
                </form>
                <ul class="nav-icon-list">
                    <li class="d-none d-sm-block d-xl-none">
                        <a href="#search" data-bs-toggle="modal" class="nav-icon-item link">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#sign" data-bs-toggle="modal" class="nav-icon-item link">
                            <i class="icon icon-User"></i>
                        </a>
                    </li>
                    <li class="d-none d-sm-block">
                        <a href="/" class="nav-icon-item link">
                            <i class="icon icon-HeartStraight"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#shoppingCart" data-bs-toggle="offcanvas" class="nav-icon-item link shop-cart">
                            <i class="icon icon-Handbag"></i>
                            <span class="count">
                                        0
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
