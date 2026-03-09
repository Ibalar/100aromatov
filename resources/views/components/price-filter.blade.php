@php
    $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;

    $absoluteMinUsd = $priceRange->min_price ?? 0;
    $absoluteMaxUsd = $priceRange->max_price ?? 1000;

    if ($absoluteMinUsd == $absoluteMaxUsd) {
        $absoluteMaxUsd = $absoluteMinUsd + 10;
    }

    $absoluteMinByn = round($absoluteMinUsd * $usdRate, 2);
    $absoluteMaxByn = round($absoluteMaxUsd * $usdRate, 2);

    $minPriceUsd = $minPrice ?? $absoluteMinUsd;
    $maxPriceUsd = $maxPrice ?? $absoluteMaxUsd;

    $minPriceByn = round($minPriceUsd * $usdRate, 2);
    $maxPriceByn = round($maxPriceUsd * $usdRate, 2);
@endphp

<div class="widget-facet">
    <div class="facet-title" data-bs-target="#filter-price" role="button"
        data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-price">
        <h6>{{ __('Цена') }}</h6>
        <span class="icon icon-CaretDown"></span>
    </div>
    <div id="filter-price" class="collapse show">
        <div class="collapse-body widget-price filter-price">
            <div class="price-val-range" id="price-value-range" data-min="{{ $absoluteMinUsd }}" data-max="{{ $absoluteMaxUsd }}" data-usd-rate="{{ $usdRate }}"></div>
            <div class="price-box tf-grid-layout tf-col-2">
                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>
                        <div class="price-val" id="price-min-value">
                            <input type="number" name="min_price_byn" class="form-control price-input-byn" placeholder="{{ number_format($absoluteMinByn, 2) }}" value="{{ request('min_price_byn', number_format($minPriceByn, 2)) }}" step="0.01" min="0">
                            <input type="hidden" name="min_price" class="price-input-usd" value="{{ $minPriceUsd }}">
                        </div>
                    </div>
                </div>
                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>
                        <div class="price-val" id="price-max-value">
                            <input type="number" name="max_price_byn" class="form-control price-input-byn" placeholder="{{ number_format($absoluteMaxByn, 2) }}" value="{{ request('max_price_byn', number_format($maxPriceByn, 2)) }}" step="0.01" min="0">
                            <input type="hidden" name="max_price" class="price-input-usd" value="{{ $maxPriceUsd }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="br-line"></div>

@push('scripts')
<script>
(function() {
    const usdRate = {{ $usdRate }};
    const priceWrapper = document.querySelector('#filter-price');

    if (priceWrapper) {
        const bynInputs = priceWrapper.querySelectorAll('.price-input-byn');
        const usdInputs = priceWrapper.querySelectorAll('.price-input-usd');

        // Convert BYN input to USD on change
        bynInputs.forEach((bynInput, index) => {
            bynInput.addEventListener('input', function() {
                const bynValue = parseFloat(this.value);
                if (!isNaN(bynValue) && usdInputs[index]) {
                    const usdValue = bynValue / usdRate;
                    usdInputs[index].value = usdValue.toFixed(2);
                }
            });

            bynInput.addEventListener('change', function() {
                const bynValue = parseFloat(this.value);
                if (!isNaN(bynValue) && usdInputs[index]) {
                    const usdValue = bynValue / usdRate;
                    usdInputs[index].value = usdValue.toFixed(2);
                }
            });
        });
    }
})();
</script>
@endpush
