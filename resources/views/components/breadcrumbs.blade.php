<section class="section-page-title flat-spacing-3">
    <div class="container">
        <div class="main-page-title">

            <div class="breadcrumbs">

                <a href="{{ url('/') }}" class="text-caption-01 cl-text-3 link">
                    Главная
                </a>

                @foreach($items as $item)

                    <i class="icon icon-CaretRightThin cl-text-3"></i>

                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}" class="text-caption-01 cl-text-3 link">
                            {{ $item['title'] }}
                        </a>
                    @else
                        <p class="text-caption-01">
                            {{ $item['title'] }}
                        </p>
                    @endif

                @endforeach

            </div>

            @if(!empty($title))
                <h3>{{ $title }}</h3>
            @endif

        </div>
    </div>
</section>

@php
    $breadcrumbs = [];
    $position = 1;

    $breadcrumbs[] = [
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => __('Главная'),
        'item' => url('/'),
    ];

    foreach ($items as $item) {
        if (! filled($item['title'] ?? null)) {
            continue;
        }

        $breadcrumbs[] = array_filter([
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => (string) $item['title'],
            'item' => filled($item['url'] ?? null) ? $item['url'] : request()->url(),
        ], static fn ($value) => $value !== null);
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    ];
@endphp

@pushOnce('schema_org')
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endPushOnce
