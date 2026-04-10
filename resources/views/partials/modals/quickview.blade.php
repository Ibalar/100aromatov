<!-- Quick View -->
<div class="offcanvas offcanvas-end canvas-quickview" id="quickView" aria-labelledby="quickViewLabel">
    <div class="mini-quick-image">
        <div class="wrap-quick" data-quickview-images>
            <div class="image">
                <div class="quickview-image-placeholder">
                    <i class="icon icon-shopping-cart-simple fs-36"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="wrap-canvas">
        <div class="canvas-header ps-md-0">
            <h5 class="title-pop" id="quickViewLabel">{{ __('Быстрый просмотр') }}</h5>
            <span class="icon-close-popup" data-bs-dismiss="offcanvas">
                <i class="icon icon-X2"></i>
            </span>
        </div>
        <div class="canvas-body ps-md-0">
            <div class="tf-product-quick_view tf-quick-prd_variant">
                <div class="tf-product-info-heading">
                    <p class="product-infor-cate text-caption-01 mb-4" data-quickview-category></p>
                    <h3 class="product-infor-name mb-12 letter-space-0" data-quickview-name>{{ __('Загрузка...') }}</h3>
                    <div class="product-infor-meta mb-20">
                        <div class="meta_rate">
                            <div class="star-wrap normal d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="icon icon-Star"></i>
                                @endfor
                            </div>
                            <span class="text-caption-01 cl-text-2" data-quickview-reviews>(0 {{ __('отзывов') }})</span>
                        </div>
                        <div class="br-line type-vertical"></div>
                        <div class="meta_sold">
                            <i class="icon icon-Tag text-primary"></i>
                            <span class="text-caption-01 cl-text-2" data-quickview-brand></span>
                        </div>
                        <div class="br-line type-vertical"></div>
                        <div class="meta_prd_code text-caption-01">
                            <span class="cl-text-2">SKU:</span>
                            <span data-quickview-sku>-</span>
                        </div>
                    </div>
                    <div class="product-infor-price mb-12">
                        <h4 class="price-on-sale" data-quickview-price>{{ __('Цена по запросу') }}</h4>
                        <div class="br-line type-vertical" data-quickview-original-separator style="display:none;"></div>
                        <p class="cl-text-3 text-decoration-line-through" data-quickview-original-price style="display:none;"></p>
                    </div>
                    <p class="product-infor-desc cl-text-2 mb-12" data-quickview-description>{{ __('Описание отсутствует') }}</p>
                    <div class="product-infor-reality lh-24" data-quickview-meta></div>
                </div>
                <div class="br-line"></div>
                <div class="tf-product-variant">
                    <div class="quick-variant-picker picker_size">
                        <div class="variant-picker_label mb-12">
                            <div>
                                {{ __('Объем') }}:
                                <span class="variant__value text-capitalize fw-medium" data-quickview-variant-label>-</span>
                            </div>
                        </div>
                        <div class="variant-picker_values" data-quickview-variants></div>
                    </div>

                    <div class="tf-product-total-quantity">
                        <p data-quickview-qty-label>{{ __('Количество') }}:</p>
                        <div class="group-action" data-quickview-order-actions>
                            <div class="wg-quantity">
                                <button type="button" class="btn-quantity js-quickview-qty-minus">
                                    <i class="icon icon-minus"></i>
                                </button>
                                <input class="quantity-product js-quickview-qty-input" type="text" name="number" value="1" readonly>
                                <button type="button" class="btn-quantity js-quickview-qty-plus">
                                    <i class="icon icon-plus"></i>
                                </button>
                            </div>
                            <button
                                type="button"
                                class="btn-action-price tf-btn type-xl animate-btn w-100 js-add-to-cart js-quickview-add-to-cart"
                                data-qty="1"
                            >
                                {{ __('Отложить') }}
                                <span class="d-none d-sm-block d-md-none d-lg-block">&nbsp;-&nbsp;</span>
                                <span class="price-add d-none d-sm-block d-md-none d-lg-block" data-quickview-button-price></span>
                            </button>
                        </div>
                        <button
                            type="button"
                            class="tf-btn type-xl btn-primary animate-btn w-100 js-buy-now js-quickview-buy-now"
                            data-qty="1"
                        >
                            {{ __('Быстрая бронь') }}
                        </button>
                        <button
                            type="button"
                            class="tf-btn type-xl btn-primary animate-btn w-100 js-open-availability-modal"
                            data-bs-toggle="modal"
                            data-bs-target="#productAvailabilityModal"
                            data-quickview-availability-button
                            style="display:none;"
                        >
                            {{ __('Уточнить наличие') }}
                        </button>
                    </div>
                </div>
                <div class="box-action">
                    <a href="{{ route('categories.index') }}" class="tf-btn-line-2 style-primary fw-semibold" data-quickview-details-link>
                        {{ __('Смотреть карточку товара') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Quick View -->

@push('styles')
    <style>
        .quickview-image-placeholder {
            min-height: 240px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f3f3f3 0%, #e7e0d7 100%);
            color: #181818;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quickview-variant-btn {
            display: inline-flex;
            flex-direction: column;
            gap: 4px;
            min-width: 96px;
            padding: 10px 12px;
            border: 1px solid #d9d9d9;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: .2s ease;
        }

        .quickview-variant-btn.active {
            border-color: #101010;
            background: #f7f7f7;
        }

        .quickview-variant-btn .meta {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
@endpush
