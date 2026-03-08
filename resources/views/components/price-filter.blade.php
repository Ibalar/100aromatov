@php
    $min = $minPrice ?? ($priceRange->min_price ?? 0);
    $max = $maxPrice ?? ($priceRange->max_price ?? 1000);
    $absoluteMin = $priceRange->min_price ?? 0;
    $absoluteMax = $priceRange->max_price ?? 1000;
@endphp

<div class="price-filter">
    <h6 class="title-filter">{{ __('Цена') }}</h6>
    <div class="price-range-wrapper">
        <div class="price-inputs">
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number"
                       name="min_price"
                       class="form-control"
                       placeholder="{{ __('От') }}"
                       value="{{ request('min_price') }}"
                       min="{{ $absoluteMin }}"
                       max="{{ $absoluteMax }}">
            </div>
            <span class="separator">-</span>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number"
                       name="max_price"
                       class="form-control"
                       placeholder="{{ __('До') }}"
                       value="{{ request('max_price') }}"
                       min="{{ $absoluteMin }}"
                       max="{{ $absoluteMax }}">
            </div>
        </div>
        <div class="price-range-slider mt-3">
            <input type="range"
                   class="form-range"
                   id="min-price-range"
                   min="{{ $absoluteMin }}"
                   max="{{ $absoluteMax }}"
                   value="{{ request('min_price', $absoluteMin) }}"
                   oninput="document.querySelector('input[name=min_price]').value = this.value">
            <input type="range"
                   class="form-range"
                   id="max-price-range"
                   min="{{ $absoluteMin }}"
                   max="{{ $absoluteMax }}"
                   value="{{ request('max_price', $absoluteMax) }}"
                   oninput="document.querySelector('input[name=max_price]').value = this.value">
        </div>
        <div class="price-labels">
            <span>${{ number_format($absoluteMin, 0) }}</span>
            <span>${{ number_format($absoluteMax, 0) }}</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .price-filter {
        margin-bottom: 1.5rem;
    }
    .price-inputs {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .price-inputs .input-group {
        flex: 1;
    }
    .price-inputs .separator {
        font-weight: bold;
    }
    .price-range-slider {
        position: relative;
    }
    .price-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #666;
        margin-top: 0.5rem;
    }
</style>
@endpush
