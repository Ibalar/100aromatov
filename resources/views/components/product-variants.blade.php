@props(['variants', 'product'])

@php
    $activeVariants = $variants->where('is_active', true)->values();
    $selectedVariant = $activeVariants->first();
@endphp

@if($activeVariants->isNotEmpty())
    <div class="variant-picker-item variant-size product-variant-picker">
        <div class="variant-picker-label">
            <div>
                {{ __('Выбери объем:') }}
            </div>
            <a href="#findSize" data-bs-toggle="modal" class="tf-btn-line-2 style-primary text-caption-01 fw-semibold">
                {{ __('Что такое тестер') }}
            </a>
        </div>

        <div class="tf-variant-dropdown full product-variant-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <input
                type="hidden"
                name="variant_id"
                id="selected-variant-id"
                value="{{ $selectedVariant?->id }}"
                data-price="{{ $selectedVariant?->final_price_usd }}"
                data-sale-price="{{ $selectedVariant?->sale_price_usd }}"
                data-original-price="{{ $selectedVariant?->price_usd }}"
                data-sku="{{ $selectedVariant?->sku }}"
                data-volume="{{ $selectedVariant?->volume_ml }}"
                data-is-preorder="{{ $selectedVariant && (float) $selectedVariant->price_usd <= 0 ? 1 : 0 }}"
            >

            <div class="btn-select">
                <span class="text-sort-value value-currentSize" id="selected-variant-dropdown-label">
                    {{ $selectedVariant?->volume_ml ? $selectedVariant->volume_ml . ' ml' : __('Вариант') }}
                </span>
                <span class="icon icon-CaretDown"></span>
            </div>

            <div class="dropdown-menu product-variant-dropdown-menu">
                @foreach($activeVariants as $variant)
                    @php
                        $badges = array_values(array_filter([
                            $variant->is_tester ? __('Тестер') : null,
                            $variant->is_raspiv ? __('Распив') : null,
                            $variant->is_unboxed ? __('Без коробки') : null,
                            $variant->is_gift_wrapped ? __('Подарочная упаковка') : null,
                            $variant->is_exclusive ? __('Отливант') : null,
                        ]));
                    @endphp

                    <div
                        class="select-item size-btn product-variant-option {{ $loop->first ? 'active' : '' }}"
                        data-variant-id="{{ $variant->id }}"
                        data-price="{{ $variant->final_price_usd }}"
                        data-sale-price="{{ $variant->sale_price_usd }}"
                        data-original-price="{{ $variant->price_usd }}"
                        data-sku="{{ $variant->sku }}"
                        data-volume="{{ $variant->volume_ml }}"
                        data-is-preorder="{{ (float) $variant->price_usd <= 0 ? 1 : 0 }}"
                    >
                        <span class="variant-option-main">
                            <span class="text-value-item variant-option-volume">{{ $variant->volume_ml }} ml</span>

                            <span class="variant-option-price">
                                @if((float) $variant->price_usd <= 0)
                                    <span class="variant-price">{{ __('Под заказ') }}</span>
                                @elseif($variant->sale_price_usd)
                                    <span class="variant-original-price">{{ formatPriceByn($variant->price_usd) }}</span>
                                    <span class="variant-sale-price">{{ formatPriceByn($variant->sale_price_usd) }}</span>
                                @else
                                    <span class="variant-price">{{ formatPriceByn($variant->price_usd) }}</span>
                                @endif
                            </span>
                            @if(!empty($badges))
                                <span class="variant-option-badges">
                                @foreach($badges as $badge)
                                        <span class="variant-option-badge">{{ $badge }}</span>
                                    @endforeach
                            </span>
                            @endif
                        </span>


                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    .product-variant-picker {
        display: grid;
        gap: 16px;
    }

    .product-variant-dropdown {
        width: 100%;
    }

    .product-variant-dropdown .btn-select {
        min-height: 52px;
    }

    .product-variant-dropdown-menu {
        width: 100%;
        padding: 8px;
    }

    .product-variant-option {
        display: grid;
        gap: 10px;
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: .3s ease;
    }

    .product-variant-option.active,
    .product-variant-option:hover {
        background: var(--bg);
    }

    .variant-option-main {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 25px;
    }

    .variant-option-volume {
        font-weight: 600;
        color: var(--text);
    }

    .variant-option-price {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .variant-original-price {
        color: var(--text-3);
        text-decoration: line-through;
        font-size: 14px;
        line-height: 20px;
    }

    .variant-sale-price,
    .variant-price {
        color: var(--text);
        font-weight: 600;
    }

    .variant-option-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .variant-option-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 999px;
        background: var(--bs-orange);
        font-size: 12px;
        line-height: 16px;
        color: var(--white);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantOptions = document.querySelectorAll('.product-variant-option');
        const variantInput = document.getElementById('selected-variant-id');
        const mainPriceElement = document.getElementById('main-product-price');
        const mainSalePriceElement = document.getElementById('main-product-sale-price');
        const skuElement = document.getElementById('main-product-sku');
        const qtyLabel = document.getElementById('product-qty-label');
        const orderActions = document.getElementById('product-order-actions');
        const availabilityButton = document.getElementById('product-availability-button');
        const usdRate = {{ \App\Models\Setting::getSettings()->usd_rate ?? 1 }};

        function updatePurchaseState(option) {
            const isPreorder = option.dataset.isPreorder === '1';
            const volume = option.dataset.volume ? option.dataset.volume + ' ml' : '';

            if (mainPriceElement) {
                if (isPreorder) {
                    mainPriceElement.textContent = @json(__('Под заказ'));
                } else {
                    const price = parseFloat(option.dataset.price);
                    const bynPrice = (price * usdRate).toFixed(2);
                    mainPriceElement.textContent = bynPrice.replace('.', ',') + ' BYN';
                }
            }

            if (mainSalePriceElement) {
                if (!isPreorder && option.dataset.salePrice) {
                    const originalPrice = parseFloat(option.dataset.originalPrice);
                    const bynPrice = (originalPrice * usdRate).toFixed(2);
                    mainSalePriceElement.textContent = bynPrice.replace('.', ',') + ' BYN';
                    mainSalePriceElement.style.display = 'inline';
                } else {
                    mainSalePriceElement.style.display = 'none';
                }
            }

            if (qtyLabel) {
                qtyLabel.style.display = isPreorder ? 'none' : '';
            }

            if (orderActions) {
                orderActions.style.display = isPreorder ? 'none' : '';
            }

            if (availabilityButton) {
                availabilityButton.style.display = isPreorder ? '' : 'none';
                availabilityButton.dataset.variantId = option.dataset.variantId || '';
                availabilityButton.dataset.variantLabel = volume;
            }
        }

        variantOptions.forEach(option => {
            option.addEventListener('click', function() {
                const volume = this.dataset.volume ? this.dataset.volume + ' ml' : '';
                const dropdownLabel = document.getElementById('selected-variant-dropdown-label');

                document.querySelectorAll('.product-variant-option').forEach(item => item.classList.remove('active'));
                this.classList.add('active');

                if (variantInput) {
                    variantInput.value = this.dataset.variantId || '';
                    variantInput.dataset.price = this.dataset.price || '';
                    variantInput.dataset.salePrice = this.dataset.salePrice || '';
                    variantInput.dataset.originalPrice = this.dataset.originalPrice || '';
                    variantInput.dataset.sku = this.dataset.sku || '';
                    variantInput.dataset.volume = this.dataset.volume || '';
                    variantInput.dataset.isPreorder = this.dataset.isPreorder || '0';
                }

                if (dropdownLabel) {
                    dropdownLabel.textContent = volume;
                }

                updatePurchaseState(this);

                if (skuElement && this.dataset.sku) {
                    skuElement.textContent = this.dataset.sku;
                }
            });
        });

        const initialOption = document.querySelector('.product-variant-option.active');

        if (initialOption) {
            updatePurchaseState(initialOption);
        }
    });
</script>
@endpush
