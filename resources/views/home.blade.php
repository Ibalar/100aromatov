@extends('layouts.app')

@section('title', __('Главная'))

@section('content')
    <!-- Banner Collection -->
    <section class="section-banner-cls">
        <div class="container-full">
            <div class="main-section">
                <div class="col-left">
                    <div class="banner-image-text type-abs style-14 h-100">
                        <a href="{{ route('categories.index') }}" class="bn-image img-style radius-20">
                            <img loading="lazy" width="1170" height="794" src="{{ asset('assets/images/catalog/hero.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="bn-content">
                            <a href="#"
                               class="title text-display fw-medium link-underline-white text-decoration-thickness_3">
                                {{ __('Широкий') }} <br>
                                {{ __('выбор') }} <br>
                                {{ __('парфюмерии') }}
                            </a>
                            <h6 class="desc text-body-1 letter-space--1">
                                {{ __('Приходите к нам за покупками в ТЦ «Немига 3»') }} <br class="d-none d-sm-block">
                                {{ __('2 этаж, маг. 41.') }}
                            </h6>
                            <a href="{{ route('categories.index') }}" class="btn-action tf-btn">
                                {{ __('В Каталог') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-right tf-grid-layout md-col-2 lg-col-1 gap-20">
                    <div class="box-image_v04 type-2">
                        <a href="{{ route('category.show', 'dlia-zenshhin') }}" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="{{ asset('assets/images/catalog/woman.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="{{ route('category.show', 'dlia-zenshhin') }}"
                               class="title h3 fw-medium link-underline-text text-decoration-thickness_3">
                                {{ __('Для женщин') }}
                            </a>
                            <a href="{{ route('category.show', 'dlia-zenshhin') }}" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        {{ __('Перейти в каталог') }}
                                    </span>
                            </a>
                        </div>
                    </div>
                    <div class="box-image_v04 type-2">
                        <a href="{{ route('category.show', 'dlia-muzcin') }}" class="box-image_img img-style">
                            <img loading="lazy" width="580" height="387" src="{{ asset('assets/images/catalog/men.jpg') }}"
                                 alt="Image">
                        </a>
                        <div class="box-image_content wow fadeInUp">
                            <a href="{{ route('category.show', 'dlia-muzcin') }}"
                               class="title h3 fw-medium text-white link-underline-text text-decoration-thickness_3">
                                {{ __('Для мужчин') }}
                            </a>
                            <a href="{{ route('category.show', 'dlia-muzcin') }}" class="btn-action tf-btn-line-2 style-primary">
                                    <span class="fw-semibold">
                                        {{ __('Подробнее') }}
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
                {{ __('Весь наш товар - оригинальный') }}
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                {{ __('Постоянные акции и распродажи') }}
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                {{ __('Профессиональная консультация при выборе или покупке') }}
            </p>
            <i class="icon icon-Lightning-1"></i>
            <p class="policy-text text-caption-02 lh-20 fw-semibold text-uppercase">
                {{ __('Выбирайте свой любимый аромат и приходите к нам!') }}
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
                        {{ __('Популярные товары') }}
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

    @if(($homeBrands ?? collect())->isNotEmpty())
        <section class="flat-spacing pt-0">
            <div class="container">
                <div class="infiniteSlide-brand wow fadeInUp">
                    <div class="infiniteSlide infiniteSlide-wrapper" data-clone="3">
                        @foreach($homeBrands as $brand)
                            <div class="img-brand">
                                <a href="{{ route('brand.show', $brand->slug) }}" class="d-flex align-items-center justify-content-center">
                                    <img
                                        loading="lazy"
                                        src="{{ asset('storage/' . $brand->logo) }}"
                                        alt="{{ $brand->name }}"
                                        style="max-height: 100px; width: auto;"
                                    >
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if(($saleProducts ?? collect())->isNotEmpty())
        <section class="flat-spacing pt-0">
            <div class="container">
                <div class="sect-heading type-2 text-center wow fadeInUp">
                    <h3 class="s-title">
                        {{ __('Акционные товары') }}
                    </h3>
                </div>

                <div class="tf-grid-layout lg-col-4 sm-col-2 grid-cls wow fadeInUp">
                    @foreach($saleProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="flat-spacing-3 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="brand-info d-flex flex-column align-items-center">
                            <div class="brand-description text-body-1 cl-text-2 mb-4">
                                <p>{{ __('Сайт 100aromatov.by является ИНТЕРНЕТ-ВИТРИНОЙ магазина парфюмерии и косметики ведущих мировых брендов, расположенного в центре Минска, в Торговом центре «Немига 3». На данном сайте Вы можете найти ассортимент нашего магазина. Очень просим Вас уточнять наличие товара по контактным телефонам и у консультанта в магазине. Мы предлагаем широкий выбор новинок парфюмерии для мужчин и женщин. Весь наш товар - оригинальный, постоянно проводятся акции и распродажи. Предусмотрена гибкая система скидок постоянным покупателям.') }}</p>
                                <p>{{ __('Мы искренне считаем своей задачей создать нашим клиентам приятную и комфортную атмосферу и предоставить профессиональную консультацию при выборе и покупке туалетной или парфюмированной воды, духов, подарочных наборов парфюмерии, косметических товаров.') }}</p>
                                <h3 class="text-center py-3"><strong>{{ __('5 АРГУМЕНТОВ В ПОЛЬЗУ МАГАЗИНА 100AROMATOV.BY') }}</strong></h3>
                                <div class="py-2">
                                    <div class="mb-3"><strong>{{ __('ШИРОКИЙ АССОРТИМЕНТ') }}</strong></div>
                                    {{ __('который постоянно обновляется. В нашем магазине можно найти не только общеизвестные парфюмерные бренды, но и нишевую парфюмерию. Кроме того, возможно индивидуально заказать тот товар, которого нет в наличии.') }}</div>

                                <div class="py-2">
                                    <div class="mb-3"><strong>{{ __('ГАРАНТИЯ КАЧЕСТВА') }}</strong></div>
                                    {{ __('Мы особое внимание уделяем работе только с проверенными парфюмерными компаниями и фирмами, гарантируя тем самым нашим клиентам оригинальность и наивысшее качество предлагаемой продукции.') }}</div>

                                <div class="py-2">
                                    <div class="mb-3"><strong>{{ __('ОПТИМАЛЬНАЯ ЦЕНА') }}</strong></div>
                                    {{ __('Мы постарались полностью отказаться от услуг посредников, работая с крупными фирмами напрямую. В тех случаях, когда это бывает невозможно, мы пользуемся услугами только проверенных поставщиков. Именно поэтому в нашем магазине такая') }} <strong>{{ __('привлекательная цена') }}</strong>.</div>

                                <div class="py-2">
                                    <div class="mb-3"><strong>{{ __('ОТЛИЧНЫЙ СЕРВИС') }}</strong></div>
                                    {{ __('Огромнейшее внимание в нашем магазине уделяется работе с клиентами. Мы считаем очень важным предоставить нашим клиентам профессиональную консультацию и оказать всевозможную помощь при подборе аромата и покупке парфюмерии и косметики. Но что еще более важно – это доброжелательное отношение, вежливость, уважение и готовность помочь.') }}</div>

                                <div class="py-2">
                                    <div class="mb-3"><strong>{{ __('ПОЖЕЛАНИЯ КЛИЕНТА') }}</strong></div>
                                    {{ __('это смысл нашего развития и работы! Мы всегда готовы выслушать ваши пожелания и недовольства, потому что это окажет незаменимую помощь при планировании дальнейшей работы. Надеемся на ваше понимание, если у нас не все еще получается.') }}</div>
                                <p><em>{{ __('Выбирайте свой любимый аромат и приходите к нам! А если у Вас возникнут сомнения или вопросы - звоните, а лучше приезжайте и наши опытные консультанты Вам с удовольствием помогут.') }}</em></p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="flat-spacing">
        <div class="container">
            <div class="sect-heading text-center wow fadeInUp">
                <h3 class="s-title">
                    {{ __('Мы в Instagram') }}
                </h3>
                <p class="s-desc">
                    {{ __('Подписывайтесь и следите за обновлением ассортимента') }}
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
                                     src="{{ asset('assets/images/gallery/inst-01.jpg') }}" alt="Image">
                            </div>
                            <a href="{{ $siteSettings->instagram_url }}" class="box-icon hover-tooltip" target="_blank">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">{{ __('Смотреть') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="{{ asset('assets/images/gallery/inst-02.jpg') }}" alt="Image">
                            </div>
                            <a href="{{ $siteSettings->instagram_url }}" class="box-icon hover-tooltip" target="_blank">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">{{ __('Смотреть') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="{{ asset('assets/images/gallery/inst-03.jpg') }}" alt="Image">
                            </div>
                            <a href="{{ $siteSettings->instagram_url }}" class="box-icon hover-tooltip" target="_blank">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">{{ __('Смотреть') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="{{ asset('assets/images/gallery/inst-04.jpg') }}" alt="Image">
                            </div>
                            <a href="{{ $siteSettings->instagram_url }}" class="box-icon hover-tooltip" target="_blank">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">{{ __('Смотреть') }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="gallery-item hover-img hover-overlay wow fadeInUp">
                            <div class="image img-style">
                                <img loading="lazy" width="274" height="274"
                                     src="{{ asset('assets/images/gallery/inst-05.jpg') }}" alt="Image">
                            </div>
                            <a href="{{ $siteSettings->instagram_url }}" class="box-icon hover-tooltip" target="_blank">
                                <span class="icon icon-Eye"></span>
                                <span class="tooltip">{{ __('Смотреть') }}</span>
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
