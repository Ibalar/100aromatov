@php
    $min = $minPrice ?? ($priceRange->min_price ?? 0);
    $max = $maxPrice ?? ($priceRange->max_price ?? 1000);

    $absoluteMin = $priceRange->min_price ?? 0;
    $absoluteMax = $priceRange->max_price ?? 1000;

    if ($absoluteMin == $absoluteMax) {
        $absoluteMax = $absoluteMin + 10;
    }

    $min = $minPrice ?? $absoluteMin;
    $max = $maxPrice ?? $absoluteMax;
@endphp

<div class="widget-facet">
    <div class="facet-title" data-bs-target="#filter-price" role="button"
        data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-price">
        <h6>{{ __('Цена') }}</h6>
        <span class="icon icon-CaretDown"></span>
    </div>
    <div id="filter-price" class="collapse show">
        <div class="collapse-body widget-price filter-price">
            <div class="price-val-range" id="price-value-range" data-min="{{ $absoluteMin }}" data-max="{{ $absoluteMax }}"></div>
            <div class="price-box tf-grid-layout tf-col-2">
                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>
                        <div class="price-val" id="price-min-value">
                            <input type="number" name="min_price" class="form-control" placeholder="{{ $absoluteMin }}" value="{{ request('min_price', $absoluteMin) }}" min="{{ $absoluteMin }}" max="{{ $absoluteMax }}">
                        </div>
                    </div>
                </div>
                <div class="box-wrap">
                    <div class="price-val_wrap">
                        <span class="cl-text-2 text-body-1">BYN</span>
                        <div class="price-val" id="price-max-value">
                            <input type="number" name="max_price" class="form-control" placeholder="{{ $absoluteMax }}" value="{{ request('max_price', $absoluteMax) }}" min="{{ $absoluteMin }}" max="{{ $absoluteMax }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="br-line"></div>
