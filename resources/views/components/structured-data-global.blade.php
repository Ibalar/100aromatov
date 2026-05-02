@php
    $phones = collect($siteSettings->phones ?? [])
        ->pluck('number')
        ->filter(static fn ($phone) => filled($phone))
        ->map(static fn ($phone) => '+' . ltrim((string) preg_replace('/\D+/', '', (string) $phone), '+'))
        ->unique()
        ->values()
        ->all();

    $sameAs = collect([
        $siteSettings->instagram_url ?? null,
        $siteSettings->telegram_url ?? null,
        $siteSettings->viber_url ?? null,
        $siteSettings->whatsapp_url ?? null,
        $siteSettings->google_reviews_url ?? null,
        $siteSettings->yandex_reviews_url ?? null,
    ])
        ->filter(static fn ($url) => filled($url))
        ->values()
        ->all();

    $store = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Store',
        '@id' => url('/#organization'),
        'name' => config('app.name'),
        'url' => url('/'),
        'logo' => asset('assets/images/logo/logo.png'),
        'email' => filled($siteSettings->email ?? null) ? $siteSettings->email : null,
        'telephone' => $phones[0] ?? null,
        'contactPoint' => $phones !== [] ? [[
            '@type' => 'ContactPoint',
            'telephone' => $phones[0],
            'contactType' => 'customer service',
            'availableLanguage' => ['ru', 'be'],
            'areaServed' => 'BY',
        ]] : null,
        'address' => filled($siteSettings->address ?? null) ? [
            '@type' => 'PostalAddress',
            'streetAddress' => $siteSettings->address,
            'addressCountry' => 'BY',
        ] : null,
        'hasMap' => filled($siteSettings->address_map_url ?? null) ? $siteSettings->address_map_url : null,
        'currenciesAccepted' => 'BYN',
        'paymentAccepted' => ['Cash', 'Credit Card', 'Bank Transfer'],
        'areaServed' => 'BY',
        'priceRange' => '$$',
        'openingHoursSpecification' => [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
            ],
            'opens' => '10:00',
            'closes' => '21:00',
        ]],
        'sameAs' => $sameAs !== [] ? $sameAs : null,
    ], static fn ($value) => $value !== null);

    $webSite = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => url('/#website'),
        'url' => url('/'),
        'name' => config('app.name'),
        'publisher' => ['@id' => url('/#organization')],
        'inLanguage' => app()->getLocale() === 'by' ? 'be-BY' : 'ru-RU',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('search') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

<script type="application/ld+json">
{!! json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode($webSite, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
