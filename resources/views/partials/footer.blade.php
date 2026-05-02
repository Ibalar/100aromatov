<footer class="tf-footer footer-s6 position-relative bg-dark">
    @php
        $footerPhones = collect($siteSettings->phones ?? [])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
        $footerPages = collect($menuPages ?? []);

        $findFooterPage = function (array $needles) use ($footerPages) {
            return $footerPages->first(function ($page) use ($needles) {
                $slug = mb_strtolower((string) ($page->slug ?? ''));
                $name = mb_strtolower((string) localizedField($page, 'name'));

                foreach ($needles as $needle) {
                    if (str_contains($slug, $needle) || str_contains($name, $needle)) {
                        return true;
                    }
                }

                return false;
            });
        };

        $salePage = $findFooterPage(['akci', 'sale', 'скид', 'распрод']);
        $shopPage = $findFooterPage(['shop', 'magazin', 'магаз', 'крам']);
        $contactsPage = $findFooterPage(['contact', 'kontakt', 'контакт']);
        $orderPage = $findFooterPage(['zakaz', 'order', 'оформ']);
        $paymentPage = $findFooterPage(['oplata', 'payment', 'оплат']);
        $deliveryPage = $findFooterPage(['delivery', 'dostav', 'самовывоз', 'дастаўк']);
        $returnPage = $findFooterPage(['return', 'vozvrat', 'обмен', 'вяртан']);
        $giftPage = $findFooterPage(['gift', 'cert', 'podar', 'сертифик', 'падар']);

        $shopLinks = collect([
            $salePage ? ['title' => __('Акции'), 'url' => route('pages.show', $salePage->slug)] : null,
            $shopPage ? ['title' => __('Наш магазин'), 'url' => route('pages.show', $shopPage->slug)] : null,
            $contactsPage ? ['title' => __('Контакты'), 'url' => route('contacts.index')] : null,
        ])->filter()->values();

        if ($shopLinks->isEmpty()) {
            $shopLinks = collect([
                ['title' => __('Каталог'), 'url' => route('categories.index')],
                ['title' => __('Бренды'), 'url' => route('brands.index')],
                ['title' => __('Контакты'), 'url' => route('contacts.index')],
            ]);
        }

        $usefulLinks = collect([
            $orderPage ? ['title' => __('Как оформить заказ'), 'url' => route('pages.show', $orderPage->slug)] : null,
            $paymentPage ? ['title' => __('Способы оплаты'), 'url' => route('pages.show', $paymentPage->slug)] : null,
            $deliveryPage ? ['title' => __('Доставка и самовывоз'), 'url' => route('pages.show', $deliveryPage->slug)] : null,
            $returnPage ? ['title' => __('Обмен и возврат товара'), 'url' => route('pages.show', $returnPage->slug)] : null,
            $giftPage ? ['title' => __('Подарочный сертификат'), 'url' => route('pages.show', $giftPage->slug)] : null,
        ])->filter()->values();

        if ($usefulLinks->isEmpty()) {
            $usefulLinks = collect([
                ['title' => __('Оформление брони'), 'url' => route('checkout.index')],
                ['title' => __('Список для бронирования'), 'url' => route('cart.index')],
                ['title' => __('Уточнить наличие'), 'url' => route('categories.index')],
            ]);
        }

        $accountLinks = collect([
            ['title' => __('Личный кабинет'), 'url' => route('customer.account.dashboard')],
            ['title' => __('Заказы'), 'url' => route('customer.account.orders')],
            ['title' => __('Профиль'), 'url' => route('customer.account.profile')],
            ['title' => __('Безопасность'), 'url' => route('customer.account.security')],
        ]);

        $usefulLinks = $usefulLinks
            ->merge($accountLinks)
            ->unique('url')
            ->values();

        $messengers = collect([
            ['key' => 'instagram', 'title' => 'Instagram', 'url' => trim((string) ($siteSettings->instagram_url ?? '')), 'abbr' => 'I'],
            ['key' => 'telegram', 'title' => 'Telegram', 'url' => trim((string) ($siteSettings->telegram_url ?? '')), 'abbr' => 'T'],
            ['key' => 'viber', 'title' => 'Viber', 'url' => trim((string) ($siteSettings->viber_url ?? '')), 'abbr' => 'V'],
            ['key' => 'whatsapp', 'title' => 'WhatsApp', 'url' => trim((string) ($siteSettings->whatsapp_url ?? '')), 'abbr' => 'W'],
        ])
            ->filter(fn (array $item): bool => filled($item['url']))
            ->values();
    @endphp
    <div class="br-line fake-class top-0"></div>
    <div class="footer-inner flat-spacing">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="footer-col-block footer-wrap-1 mb-lg-0">
                        <p class="footer-heading footer-heading-mobile text-white">{{ __('О Нас') }}</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-menu-list">
                                <li><a href="{{ route('home') }}" class="cl-text-2 link">{{ __('Главная') }}</a></li>
                                <li><a href="{{ route('categories.index') }}" class="cl-text-2 link">{{ __('Каталог') }}</a></li>
                                <li><a href="{{ route('brands.index') }}" class="cl-text-2 link">{{ __('Бренды') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="footer-col-block footer-wrap-2 mb-lg-0">
                        <p class="footer-heading footer-heading-mobile text-white">{{ __('Наш магазин') }}</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-menu-list">
                                @foreach($shopLinks as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}" class="cl-text-2 link" @if(str_starts_with($link['url'], 'http')) rel="noopener noreferrer" @endif>
                                            {{ $link['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <a href="{{ route('pages.show', 'podarocnyi-sertifikat') }}" class="cl-text-2 link">
                                        {{ __('Подарочный сертификат') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="footer-col-block footer-wrap-2 mb-lg-0">
                        <p class="footer-heading footer-heading-mobile text-white">{{ __('Полезное') }}</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-menu-list">
                                @foreach($usefulLinks as $link)
                                    <li><a href="{{ $link['url'] }}" class="cl-text-2 link">{{ $link['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="footer-col-block footer-wrap-3 mb-0">
                        <p class="footer-heading footer-heading-mobile text-white">{{ __('Наши контакты') }}</p>
                        <div class="tf-collapse-content">
                            @if($footerPhones->isNotEmpty())
                                <p class="cl-text-2 mb-4">{{ __('Телефоны') }}:</p>
                                <div class="d-flex flex-column gap-8 mb-12">
                                    @foreach($footerPhones as $phone)
                                        <a href="{{ phoneHref($phone['number'] ?? null) }}" class="link h6 fw-medium d-inline-flex align-items-center gap-2 text-white">
                                            @if($iconUrl = settingPhoneIconUrl($phone['icon'] ?? null))
                                                <img src="{{ $iconUrl }}"
                                                     alt="{{ $phone['label'] ?? ($phone['number'] ?? __('Телефон')) }}"
                                                     width="20"
                                                     height="20">
                                            @endif
                                            <span>{{ $phone['number'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @if(filled($siteSettings->address ?? null))
                                @if(filled($siteSettings->address_map_url ?? null))
                                    <a href="{{ $siteSettings->address_map_url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="cl-text-2 link mb-12 d-inline-block">
                                        {{ $siteSettings->address }}
                                    </a>
                                @else
                                    <p class="cl-text-2 mb-12">{{ $siteSettings->address }}</p>
                                @endif
                            @endif

                            @php
                                $yandexRating = $siteSettings->yandex_rating !== null ? number_format((float) $siteSettings->yandex_rating, 1, '.', '') : null;
                                $yandexReviewsCount = $siteSettings->yandex_reviews_count;
                                $yandexReviewsUrl = trim((string) ($siteSettings->yandex_reviews_url ?? ''));
                            @endphp

                            @if($yandexRating !== null)
                                @if($yandexReviewsUrl !== '')
                                    <a href="{{ $yandexReviewsUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="cl-text-2 link mb-12 d-inline-block">
                                        {{ __('Яндекс Карты') }}: ★ {{ $yandexRating }}
                                        @if($yandexReviewsCount !== null)
                                            ({{ $yandexReviewsCount }} {{ __('отзывов') }})
                                        @endif
                                    </a>
                                @else
                                    <p class="cl-text-2 mb-12">
                                        {{ __('Яндекс Карты') }}: ★ {{ $yandexRating }}
                                        @if($yandexReviewsCount !== null)
                                            ({{ $yandexReviewsCount }} {{ __('отзывов') }})
                                        @endif
                                    </p>
                                @endif
                            @endif

                            @if($messengers->isNotEmpty())
                                <div class="d-flex align-items-center gap-12">
                                    @foreach($messengers as $messenger)
                                        <a href="{{ $messenger['url'] }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="messenger-icon messenger-icon--{{ $messenger['key'] }}"
                                           aria-label="{{ $messenger['title'] }}"
                                           title="{{ $messenger['title'] }}">
                                            @include('partials.messenger-icon', ['key' => $messenger['key']])
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    @if(filled($siteSettings->requisites ?? null))
                        <div class="cl-text-2 mb-12" style="white-space: pre-line;">{{ $siteSettings->requisites }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="br-line d-none d-sm-block"></div>
            <div class="inner-bottom">
                <p class="text-nocopy cl-text-2">
                    © 2018-{{ date('Y') }} {{ __('Все права защищены.') }}
                </p>
                <ul class="tf-list payment-list">
                    <li class="cl-text-2">Разработка сайта <a class="text-white ps-2" href="https://webart.by">WebArt BY</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

@if($messengers->isNotEmpty())
    <div id="wcw-wrap" class="messenger-widget" data-messenger-widget>
        <button type="button" class="messenger-fab" data-messenger-fab aria-expanded="false" aria-label="{{ __('Открыть мессенджеры') }}">
            <span class="messenger-fab__wave messenger-fab__wave--1" aria-hidden="true"></span>
            <span class="messenger-fab__wave messenger-fab__wave--2" aria-hidden="true"></span>
            @foreach($messengers as $index => $messenger)
                <span class="messenger-fab__icon-item messenger-icon messenger-icon--{{ $messenger['key'] }} @if($index === 0) is-active @endif" data-messenger-rotating-icon>
                    @include('partials.messenger-icon', ['key' => $messenger['key']])
                </span>
            @endforeach
        </button>
        <div class="messenger-widget__panel" data-messenger-panel>
            @foreach($messengers as $messenger)
                <a href="{{ $messenger['url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="messenger-widget__item"
                   aria-label="{{ $messenger['title'] }}">
                    <span class="messenger-icon messenger-icon--{{ $messenger['key'] }}">
                        @include('partials.messenger-icon', ['key' => $messenger['key']])
                    </span>
                    <span class="messenger-widget__label">{{ $messenger['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<style>
    .messenger-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        line-height: 1;
        transition: transform .2s ease, opacity .2s ease;
    }

    .messenger-icon__svg {
        width: 100%;
        height: 100%;
        display: inline-flex;
    }

    .messenger-icon--telegram {
        background: transparent;
    }

    .messenger-icon--viber {
        background: transparent;
    }

    .messenger-icon--whatsapp {
        background: transparent;
    }

    #wcw-wrap.messenger-widget {
        position: fixed;
        left: 24px;
        bottom: 84px;
        z-index: 10020;
        width: 70px;
        height: 70px;
        overflow: visible;
    }

    #wcw-wrap .messenger-fab {
        width: 70px;
        height: 70px;
        border: 0;
        border-radius: 50%;
        background: #1A2A2D;
        box-shadow: 0 20px 46px rgba(0, 0, 0, 0.38);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        overflow: visible;
        z-index: 2;
    }

    #wcw-wrap .messenger-fab__wave {
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        border: 2px solid rgba(67, 217, 173, 0.85);
        pointer-events: none;
        transform: scale(1);
        opacity: 0;
        animation: messengerFabWave 2.4s ease-out infinite;
        z-index: 0;
    }

    #wcw-wrap .messenger-fab__wave--2 {
        animation-delay: 1.2s;
    }

    #wcw-wrap.messenger-widget.is-open .messenger-fab {
        box-shadow: 0 20px 46px rgba(0, 0, 0, 0.38);
    }

    #wcw-wrap.messenger-widget.is-open .messenger-fab__wave {
        animation: none;
        opacity: 0;
    }

    #wcw-wrap .messenger-fab__icon-item {
        position: absolute;
        opacity: 0;
        transform: scale(0.6);
        width: 42px;
        height: 42px;
        z-index: 1;
    }

    #wcw-wrap .messenger-fab__icon-item.is-active {
        opacity: 1;
        transform: scale(1);
    }

    #wcw-wrap .messenger-widget__panel {
        position: absolute;
        left: 0;
        bottom: 106px;
        min-width: 320px;
        border-radius: 18px;
        background: #111;
        padding: 14px;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.42);
        visibility: hidden;
        opacity: 0;
        transform: translateY(10px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
    }

    #wcw-wrap.messenger-widget.is-open .messenger-widget__panel {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }

    #wcw-wrap .messenger-widget__item {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #fff;
        text-decoration: none;
        padding: 12px;
        border-radius: 12px;
    }

    #wcw-wrap .messenger-widget__item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    #wcw-wrap .messenger-widget__label {
        font-size: 20px;
        line-height: 1.2;
    }

    @keyframes messengerFabWave {
        0% {
            transform: scale(1);
            opacity: 0.85;
        }
        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    @media (max-width: 991px) {
        #wcw-wrap.messenger-widget {
            display: none !important;
        }
    }
</style>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const widget = document.querySelector('#wcw-wrap[data-messenger-widget]');

                if (!widget) {
                    return;
                }

                const button = widget.querySelector('[data-messenger-fab]');
                const panel = widget.querySelector('[data-messenger-panel]');
                const rotatingIcons = Array.from(widget.querySelectorAll('[data-messenger-rotating-icon]'));
                let activeIndex = 0;

                const setExpandedState = function (expanded) {
                    widget.classList.toggle('is-open', expanded);
                    button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                };

                if (rotatingIcons.length > 1) {
                    window.setInterval(function () {
                        rotatingIcons[activeIndex].classList.remove('is-active');
                        activeIndex = (activeIndex + 1) % rotatingIcons.length;
                        rotatingIcons[activeIndex].classList.add('is-active');
                    }, 1800);
                }

                button.addEventListener('click', function () {
                    const isOpen = widget.classList.contains('is-open');
                    setExpandedState(!isOpen);
                });

                panel.addEventListener('click', function () {
                    setExpandedState(false);
                });

                document.addEventListener('click', function (event) {
                    if (!widget.contains(event.target)) {
                        setExpandedState(false);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setExpandedState(false);
                    }
                });
            });
        </script>
    @endpush
@endonce
