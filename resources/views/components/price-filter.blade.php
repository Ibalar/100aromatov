@php
    $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;

    $absoluteMinUsd = $priceRange->min_price ?? 0;
    $absoluteMaxUsd = $priceRange->max_price ?? 1000;

    if ($absoluteMinUsd == $absoluteMaxUsd) {
        $absoluteMaxUsd = $absoluteMinUsd + 10;
    }

    // convert to BYN
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

            <div
                class="price-val-range"
                id="price-value-range"
                data-min="{{ $absoluteMinByn }}"
                data-max="{{ $absoluteMaxByn }}"
                data-usd-rate="{{ $usdRate }}">
            </div>

            <div class="price-box tf-grid-layout tf-col-2">

                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>

                        <div class="price-val" id="price-min-value">
                            <input
                                type="number"
                                name="min_price_byn"
                                class="form-control price-input-byn"
                                value="{{ request('min_price_byn', $minPriceByn) }}"
                                step="0.01"
                                min="0">

                            <input
                                type="hidden"
                                name="min_price"
                                class="price-input-usd"
                                value="{{ $minPriceUsd }}">
                        </div>
                    </div>
                </div>

                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>

                        <div class="price-val" id="price-max-value">
                            <input
                                type="number"
                                name="max_price_byn"
                                class="form-control price-input-byn"
                                value="{{ request('max_price_byn', $maxPriceByn) }}"
                                step="0.01"
                                min="0">

                            <input
                                type="hidden"
                                name="max_price"
                                class="price-input-usd"
                                value="{{ $maxPriceUsd }}">
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

        (function(){

            const range = document.querySelector('#price-value-range');
            if(!range) return;

            const usdRate = parseFloat(range.dataset.usdRate);

            const bynInputs = document.querySelectorAll('.price-input-byn');
            const usdInputs = document.querySelectorAll('.price-input-usd');

            function convert(index) {

                const byn = parseFloat(bynInputs[index].value);

                if(!isNaN(byn)){
                    usdInputs[index].value = (byn / usdRate).toFixed(2);
                }

            }

            bynInputs.forEach((input,index)=>{

                // convert on page load
                convert(index);

                input.addEventListener('input',()=>convert(index));

            });

        })();

    </script>
@endpush
