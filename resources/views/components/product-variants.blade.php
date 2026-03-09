@props(['variants', 'product'])

@php
    $activeVariants = $variants->where('is_active', true);
@endphp

@if($activeVariants->count() > 0)
    <div class="product-variants-selector">
        <h6 class="variant-title">{{ __('Выберите объем') }}</h6>
        <div class="variants-list">
            @foreach($activeVariants as $variant)
                <label class="variant-item" data-variant-id="{{ $variant->id }}">
                    <input type="radio"
                           name="variant_id"
                           value="{{ $variant->id }}"
                           data-price="{{ $variant->final_price_usd }}"
                           data-sale-price="{{ $variant->sale_price_usd }}"
                           data-original-price="{{ $variant->price_usd }}"
                           {{ $loop->first ? 'checked' : '' }}>
                    <span class="variant-content">
                        <span class="variant-volume">{{ $variant->volume_ml }} ml</span>
                        <span class="variant-price-wrapper">
                            @if($variant->sale_price_usd)
                                <span class="variant-original-price">{{ formatPriceByn($variant->price_usd) }}</span>
                                <span class="variant-sale-price">{{ formatPriceByn($variant->sale_price_usd) }}</span>
                            @else
                                <span class="variant-price">{{ formatPriceByn($variant->price_usd) }}</span>
                            @endif
                        </span>
                        @if($variant->is_tester)
                            <span class="variant-badge tester">{{ __('Тестер') }}</span>
                        @endif
                        @if($variant->is_raspiv)
                            <span class="variant-badge raspiv">{{ __('Распив') }}</span>
                        @endif
                        @if($variant->is_unboxed)
                            <span class="variant-badge unboxed">{{ __('Без коробки') }}</span>
                        @endif
                        @if($variant->is_gift_wrapped)
                            <span class="variant-badge gift">{{ __('Подарочная упаковка') }}</span>
                        @endif
                        @if($variant->is_exclusive)
                            <span class="variant-badge exclusive">{{ __('Эксклюзив') }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    </div>
@endif

@push('styles')
<style>
    .product-variants-selector {
        margin-bottom: 2rem;
    }
    .variant-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .variants-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .variant-item {
        cursor: pointer;
        flex: 1;
        min-width: 140px;
        max-width: 200px;
    }
    .variant-item input[type="radio"] {
        display: none;
    }
    .variant-content {
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.2s;
        background: #fff;
    }
    .variant-item input[type="radio"]:checked + .variant-content {
        border-color: #007bff;
        background: #f0f7ff;
    }
    .variant-volume {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .variant-price-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .variant-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: #333;
    }
    .variant-original-price {
        font-size: 0.875rem;
        text-decoration: line-through;
        color: #999;
    }
    .variant-sale-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: #dc3545;
    }
    .variant-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }
    .variant-badge.tester {
        background: #e3f2fd;
        color: #1976d2;
    }
    .variant-badge.raspiv {
        background: #fce4ec;
        color: #c2185b;
    }
    .variant-badge.unboxed {
        background: #f3e5f5;
        color: #7b1fa2;
    }
    .variant-badge.gift {
        background: #e8f5e9;
        color: #388e3c;
    }
    .variant-badge.exclusive {
        background: #fff3e0;
        color: #f57c00;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantInputs = document.querySelectorAll('input[name="variant_id"]');
        const mainPriceElement = document.getElementById('main-product-price');
        const mainSalePriceElement = document.getElementById('main-product-sale-price');
        const skuElement = document.getElementById('main-product-sku');
        const usdRate = {{ \App\Models\Setting::getSettings()->usd_rate ?? 1 }};

        variantInputs.forEach(input => {
            input.addEventListener('change', function() {
                const price = parseFloat(this.dataset.price);
                const salePrice = this.dataset.salePrice ? parseFloat(this.dataset.salePrice) : null;
                const originalPrice = parseFloat(this.dataset.originalPrice);

                if (mainPriceElement) {
                    const bynPrice = (price * usdRate).toFixed(2);
                    mainPriceElement.textContent = bynPrice.replace('.', ',') + ' BYN';
                }
                if (mainSalePriceElement) {
                    if (salePrice) {
                        const bynPrice = (originalPrice * usdRate).toFixed(2);
                        mainSalePriceElement.textContent = bynPrice.replace('.', ',') + ' BYN';
                        mainSalePriceElement.style.display = 'inline';
                    } else {
                        mainSalePriceElement.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
@endpush
