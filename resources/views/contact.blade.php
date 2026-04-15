@extends('layouts.app')

@section('title', __('Контакты') . ' - ' . config('app.name'))

@section('content')
    @php
        $contactPhones = collect($siteSettings->phones ?? [])->filter(fn ($phone) => filled($phone['number'] ?? null))->values();
        $address = trim((string) ($siteSettings->address ?? ''));
        $addressMapUrl = trim((string) ($siteSettings->address_map_url ?? ''));
        $requisites = trim((string) ($siteSettings->requisites ?? ''));
        $instagramUrl = trim((string) ($siteSettings->instagram_url ?? ''));

        $email = trim((string) ($contactEmail ?? $siteSettings->email ?? ''));
        if ($email === '' && preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $requisites, $emailMatch)) {
            $email = $emailMatch[0];
        }

        $mapLink = $addressMapUrl !== '' ? $addressMapUrl : ('https://www.google.com/maps?q=' . urlencode($address !== '' ? $address : 'Minsk'));
        $isEmbedMap = $addressMapUrl !== '' && (str_contains($addressMapUrl, 'google.com/maps/embed') || str_contains($addressMapUrl, 'yandex.ru/map-widget'));
        $mapEmbedUrl = $isEmbedMap ? $addressMapUrl : ('https://www.google.com/maps?q=' . urlencode($address !== '' ? $address : 'Minsk') . '&output=embed');
    @endphp

    <section class="section-page-title text-center flat-spacing-2 pb-0">
        <div class="container">
            <div class="main-page-title">
                <div class="breadcrumbs">
                    <a href="{{ route('home') }}" class="text-caption-01 cl-text-3 link">{{ __('Главная') }}</a>
                    <i class="icon icon-CaretRightThin cl-text-3"></i>
                    <p class="text-caption-01">{{ __('Контакты') }}</p>
                </div>
            </div>
        </div>
    </section>



    <section class="section-contact flat-spacing pb-1">
        <div class="container">
            <div class="row gy-5 flex-wrap-reverse">
                <div class="col-md-6">
                    <div class="col-left">
                        <div class="heading d-grid gap-8">
                            <h4>{{ __('Контакты') }}</h4>
                        </div>
                        <div class="grid-info tf-grid-layout sm-col-2">
                            <div class="d-grid gap-8">
                                <h6>{{ __('Телефон') }}:</h6>
                                <div class="wd-full d-grid gap-4">
                                    @forelse($contactPhones as $phone)
                                        <a href="{{ phoneHref($phone['number'] ?? null) }}" class="cl-text-2 link d-inline-flex align-items-center gap-2">
                                            <span>
                                                {{ $phone['number'] }}
                                                @if($phone['label'])
                                                    ({{ $phone['label'] }})
                                                @endif
                                            </span>
                                        </a>
                                    @empty
                                        <span class="cl-text-2">-</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="wd-full d-grid gap-8">
                                <h6>Email:</h6>
                                <p>
                                    @if($email)
                                        <a href="mailto:{{ $email }}" class="cl-text-2 link">{{ $email }}</a>
                                    @else
                                        <span class="cl-text-2">-</span>
                                    @endif
                                </p>
                            </div>
                            <div class="wd-full d-grid gap-8">
                                <h6>{{ __('Адрес') }}:</h6>
                                <p>
                                    @if($address !== '')
                                        <a href="{{ $mapLink }}" target="_blank" rel="noopener noreferrer" class="cl-text-2 link">
                                            {{ $address }}
                                        </a>
                                    @else
                                        <span class="cl-text-2">-</span>
                                    @endif
                                </p>
                            </div>

                            @if($instagramUrl !== '')
                                <div class="wd-full d-grid gap-8">
                                    <h6>Instagram:</h6>
                                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="cl-text-2 link">
                                        {{ __('Открыть профиль') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4 class="mb-8">{{ __('Реквизиты') }}</h4>
                    <div class="wd-full d-grid gap-8">
                        @if($requisites !== '')
                            <div class="cl-text-2" style="white-space: pre-line;">{{ $requisites }}</div>
                        @else
                            <div class="cl-text-2">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-map flat-spacing-2 pb-3">
        <div class="container">
            <div class="wg-map">
                <iframe
                    src="{{ $mapEmbedUrl }}"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
@endsection
