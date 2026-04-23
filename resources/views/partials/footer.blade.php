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



                            @if(filled($siteSettings->instagram_url ?? null))
                                <div class="d-flex align-items-center gap-20">
                                    <a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="d-flex">
                                        <i class="fs-20 link icon icon-InstagramLogo"></i>
                                    </a>
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
                    <li class="cl-text-2">Разработка сайта <a class="text-white ps-2" href="https://webart.by"> WebArt BY</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
