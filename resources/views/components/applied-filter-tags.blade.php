@props([
    'clearUrl',
    'brands' => collect(),
    'filterAttributes' => collect(),
    'selectedAttributes' => [],
    'brandFilter' => null,
    'minPrice' => null,
    'maxPrice' => null,
    'priceRange' => null,
])

@php
    $query = request()->query();
    $appliedFilters = [];

    $absoluteMinPrice = $priceRange->min_price ?? null;
    $absoluteMaxPrice = $priceRange->max_price ?? null;

    $hasPriceFilter = ($minPrice !== null && $minPrice !== '' && (string) $minPrice !== (string) $absoluteMinPrice)
        || ($maxPrice !== null && $maxPrice !== '' && (string) $maxPrice !== (string) $absoluteMaxPrice);

    if ($hasPriceFilter) {
        $priceQuery = $query;
        unset($priceQuery['min_price'], $priceQuery['max_price'], $priceQuery['min_price_byn'], $priceQuery['max_price_byn'], $priceQuery['page']);

        $priceLabel = __('Цена') . ': ';
        $priceLabel .= ($minPrice !== null && $minPrice !== '' ? formatPriceByn((float) $minPrice) : '0');
        $priceLabel .= ' - ';
        $priceLabel .= ($maxPrice !== null && $maxPrice !== '' ? formatPriceByn((float) $maxPrice) : __('Любая'));

        $appliedFilters[] = [
            'label' => $priceLabel,
            'url' => request()->url() . ($priceQuery !== [] ? '?' . http_build_query($priceQuery) : ''),
        ];
    }

    $selectedBrandIds = array_values(array_filter((array) $brandFilter, static fn ($value) => $value !== ''));

    foreach ($selectedBrandIds as $selectedBrandId) {
        $brand = $brands->firstWhere('id', (int) $selectedBrandId);

        if (! $brand) {
            continue;
        }

        $brandQuery = $query;

        if (isset($brandQuery['brand'])) {
            $brandValues = array_values(array_filter((array) $brandQuery['brand'], static fn ($value) => (string) $value !== (string) $selectedBrandId));

            if ($brandValues === []) {
                unset($brandQuery['brand']);
            } else {
                $brandQuery['brand'] = $brandValues;
            }
        }

        unset($brandQuery['page']);

        $appliedFilters[] = [
            'label' => __('Бренд') . ': ' . $brand->name,
            'url' => request()->url() . ($brandQuery !== [] ? '?' . http_build_query($brandQuery) : ''),
        ];
    }

    foreach ($filterAttributes as $attribute) {
        $attributeId = $attribute->id;
        $selectedValueIds = array_values(array_filter((array) ($selectedAttributes[$attributeId] ?? []), static fn ($value) => $value !== '' && $value !== 'all'));

        foreach ($selectedValueIds as $selectedValueId) {
            $value = $attribute->values->firstWhere('id', (int) $selectedValueId);

            if (! $value) {
                continue;
            }

            $attributeQuery = $query;
            $attributeQueryValues = array_values(array_filter(
                (array) data_get($attributeQuery, "attributes.$attributeId", []),
                static fn ($valueId) => (string) $valueId !== (string) $selectedValueId
            ));

            if ($attributeQueryValues === []) {
                unset($attributeQuery['attributes'][$attributeId]);
            } else {
                $attributeQuery['attributes'][$attributeId] = $attributeQueryValues;
            }

            if (isset($attributeQuery['attributes']) && $attributeQuery['attributes'] === []) {
                unset($attributeQuery['attributes']);
            }

            unset($attributeQuery['page']);

            $appliedFilters[] = [
                'label' => localizedField($attribute, 'name') . ': ' . localizedField($value, 'value'),
                'url' => request()->url() . ($attributeQuery !== [] ? '?' . http_build_query($attributeQuery) : ''),
            ];
        }
    }
@endphp

@if($appliedFilters !== [])
    @once
        @push('styles')
            <style>
                .applied-filter-tags {
                    margin-top: 12px;
                }

                .applied-filter-tags__list {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .applied-filter-tags__item {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 6px 10px;
                    border: 1px solid rgba(24, 24, 24, 0.12);
                    border-radius: 999px;
                    font-size: 13px;
                    line-height: 1.3;
                }

                .applied-filter-tags__clear {
                    display: inline-block;
                    margin-top: 10px;
                    font-size: 13px;
                    text-decoration: underline;
                }
            </style>
        @endpush
    @endonce

    <div class="applied-filter-tags pb-3 mb-3">
        <div class="applied-filter-tags__list">
            @foreach($appliedFilters as $filter)
                <a href="{{ $filter['url'] }}" class="applied-filter-tags__item">
                    <span>{{ $filter['label'] }}</span>
                    <i class="icon icon-X2"></i>
                </a>
            @endforeach
        </div>
        <a href="{{ $clearUrl }}" class="applied-filter-tags__clear">{{ __('Сбросить все') }}</a>
    </div>
@endif
