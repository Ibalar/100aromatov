@props([
    'items' => [],
])

@php
    $faqItems = collect($items)
        ->filter(static fn ($item) => filled($item['q'] ?? null) && filled($item['a'] ?? null))
        ->map(static fn ($item) => [
            '@type' => 'Question',
            'name' => trim((string) $item['q']),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => trim((string) $item['a']),
            ],
        ])
        ->values()
        ->all();

    if ($faqItems === []) {
        return;
    }

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqItems,
    ];
@endphp

@pushOnce('schema_org')
    <script type="application/ld+json">
        {!! json_encode($faqSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endPushOnce
