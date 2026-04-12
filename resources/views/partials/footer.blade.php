<footer class="tf-footer footer-s6 position-relative bg-dark">
    @php
        $footerPhones = collect($siteSettings->phones ?? [])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
    @endphp
    <div class="br-line fake-class top-0"></div>
    <div class="footer-inner flat-spacing">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="footer-col-block footer-wrap-3 mb-md-0 ms-0">
                        <p class="footer-heading footer-heading-mobile text-white">CONTACTS</p>
                        <div class="tf-collapse-content">
                            @if($footerPhones->isNotEmpty())
                                <p class="cl-text-2 mb-4">Телефоны:</p>
                                <div class="d-flex flex-column gap-8 mb-16">
                                    @foreach($footerPhones as $phone)
                                        <a href="{{ phoneHref($phone['number'] ?? null) }}" class="link h6 fw-medium d-inline-flex align-items-center gap-2 text-white">
                                            @if($iconUrl = settingPhoneIconUrl($phone['icon'] ?? null))
                                                <img src="{{ $iconUrl }}"
                                                     alt="{{ $phone['label'] ?? ($phone['number'] ?? 'Phone') }}"
                                                     width="20"
                                                     height="20">
                                            @endif
                                            <span>{{ $phone['number'] }}</span>
                                            @if(filled($phone['label'] ?? null))
                                                <span class="cl-text-2">({{ $phone['label'] }})</span>
                                            @endif
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

                            @if(filled($siteSettings->requisites ?? null))
                                <div class="cl-text-2 mb-12" style="white-space: pre-line;">{{ $siteSettings->requisites }}</div>
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
                <div class="col-sm-6 col-md-6 col-lg-2">
                    <div class="footer-col-block footer-wrap-1 mx-xl-auto mb-lg-0">
                        <p class="footer-heading footer-heading-mobile text-white">COMPANY</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-menu-list">
                                <li><a href="about.html" class="cl-text-2 link">About Us</a></li>
                                <li><a href="our-store.html" class="cl-text-2 link">Our Stories</a></li>
                                <li><a href="contact.html" class="cl-text-2 link">Contact us</a></li>
                                <li><a href="blog.html" class="cl-text-2 link">Latest New</a></li>
                                <li><a href="account-page.html" class="cl-text-2 link">My Account</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-2">
                    <div class="footer-col-block footer-wrap-2 mx-xl-auto mb-lg-0">
                        <p class="footer-heading footer-heading-mobile text-white">CUSTOMER</p>
                        <div class="tf-collapse-content">
                            <ul class="footer-menu-list">
                                <li><a href="shipping.html" class="cl-text-2 link">Shipping</a></li>
                                <li><a href="return-and-refund.html" class="cl-text-2 link">Return & Refund</a>
                                </li>
                                <li><a href="privacy-policy.html" class="cl-text-2 link">Privacy Policy</a></li>
                                <li><a href="term-and-condition.html" class="cl-text-2 link">Terms &
                                        Conditions</a></li>
                                <li><a href="faq.html" class="cl-text-2 link">Orders FAQs</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="footer-col-block footer-wrap-3 ms-0 ms-lg-auto mb-0">
                        <p class="footer-heading footer-heading-mobile text-white">NEWSLETTER</p>
                        <div class="tf-collapse-content">
                            <p class="footer-desc cl-text-2 mb-16">
                                Subscribe for store updates and discounts.
                            </p>
                            <form class="form-sub">
                                <fieldset>
                                    <input type="email" placeholder="Enter your e-mail" required>
                                </fieldset>
                                <button type="submit" class="btn-action">
                                    <i class="icon icon-ArrowUpRight"></i>
                                </button>
                            </form>
                            <p class="text-remember cl-text-2">
                                By clicking subcribe, you agree to the
                                <a href="term-and-condition.html" class="link link-underline">
                                    Terms of Service
                                </a>
                                and
                                <a href="privacy-policy.html" class="link link-underline">
                                    Privacy Policy
                                </a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="br-line d-none d-sm-block"></div>
            <div class="inner-bottom">
                <div class="tf-list list-currenci">
                    <div class="tf-currencies">
                        <select class="tf-dropdown-select style-default color-text-2 type-currencies">
                            <option selected data-thumbnail="assets/images/country/us.png">United States (USD $)
                            </option>
                            <option data-thumbnail="assets/images/country/vn.png">Viet Nam (VND ₫)</option>
                        </select>
                    </div>
                    <div class="tf-languages">
                        <select class="tf-dropdown-select style-default color-text-2 type-languages">
                            <option>English</option>
                            <option>العربية</option>
                            <option>简体中文</option>
                            <option>اردو</option>
                        </select>
                    </div>
                </div>
                <p class="text-nocopy cl-text-2">
                    ©2026 Amerce. All Rights Reserved.
                </p>
                <ul class="tf-list payment-list">
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/visa.svg"
                             alt="Image"></li>
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/master-card.svg"
                             alt="Image"></li>
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/amex.svg"
                             alt="Image"></li>
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/paypal.svg"
                             alt="Image"></li>
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/water.svg"
                             alt="Image"></li>
                    <li><img loading="lazy" width="38" height="24" src="assets/images/payment/discover.svg"
                             alt="Image"></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
