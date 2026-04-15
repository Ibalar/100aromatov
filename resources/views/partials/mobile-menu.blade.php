<!-- Mobile Menu -->
@php
    $mobilePhones = collect($siteSettings->phones ?? [])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
@endphp
<div class="offcanvas offcanvas-start canvas-mb" id="mobileMenu">
    <div class="canvas-header">
            <span class="icon-close-popup" data-bs-dismiss="offcanvas">
                <i class="icon icon-X2"></i>
            </span>
        <form action="{{ route('search') }}" method="GET" class="form-search-nav">
            <fieldset>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Искать на сайте') }}" required>
            </fieldset>
            <button type="submit" class="btn-action">
                <i class="icon icon-MagnifyingGlass"></i>
            </button>
        </form>
    </div>
    <div class="canvas-body">
        <div class="mb-content-top">
            <ul class="nav-ul-mb" id="wrapper-menu-navigation"></ul>
        </div>
        <div class="need-help-wrap">
            <p class="nd-title h6 fw-medium mb-16">{{ __('Нужна помощь?') }}</p>
            @if(filled($siteSettings->address ?? null))
                <p class="lh-26 cl-text-2 mb-4">
                    {{ $siteSettings->address }}
                </p>
            @endif
            @if(filled($siteSettings->address_map_url ?? null))
                <a href="{{ $siteSettings->address_map_url }}" target="_blank" rel="noopener noreferrer"
                   class="text-decoration-underline text-primary lh-26 mb-16">
                    {{ __('Открыть на карте') }}
                </a>
            @endif
            @foreach($mobilePhones as $phone)
                <a href="{{ phoneHref($phone['number'] ?? null) }}" class="cl-text-2 link mb-8 d-inline-flex align-items-center gap-2">
                    <span>{{ $phone['number'] }}</span>
                    @if($phone['label'])
                       ( {{ $phone['label'] }} )
                    @endif

                </a>
            @endforeach
            @if(filled($siteSettings->instagram_url ?? null))
                <a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer" class="cl-text-2 link">
                    {{ __('Instagram') }}
                </a>
            @endif
        </div>
    </div>
    <div class="canvas-footer">
        <div class="d-flex justify-content-center">
            <div class="tf-languages">
                <select class="tf-dropdown-select style-default type-languages"
                        onchange="if (this.value) { window.location.href = this.value; }">
                    <option value="{{ route('language.switch', 'ru') }}" @selected(app()->getLocale() === 'ru')>
                        {{ __('Русский') }}
                    </option>
                    <option value="{{ route('language.switch', 'by') }}" @selected(app()->getLocale() === 'by')>
                        {{ __('Беларуская') }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>
<!-- /Mobile Menu -->
