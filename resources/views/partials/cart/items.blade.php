@forelse($items as $item)
    <div class="tf-mini-cart-item cart-item" data-variant-id="{{ $item['variant_id'] }}">
        <div class="tf-mini-cart-image">
            <a href="{{ route('product.show', $item['product_slug']) }}">
                @if($item['image'])
                    <img loading="lazy" width="100" height="133" src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['product_name'] }}">
                @endif
            </a>
        </div>
        <div class="tf-mini-cart-info">
            <a href="{{ route('product.show', $item['product_slug']) }}" class="name fw-medium link text-line-clamp-1">
                {{ $item['product_name'] }}
            </a>
            <div class="text-caption-01 cl-text-3">SKU: {{ $item['sku'] }}</div>
            @if($item['volume_ml'])
                <div class="text-caption-01 cl-text-3">{{ $item['volume_ml'] }} ml</div>
            @endif
            <div class="text-caption-01 fw-medium mt-4 js-cart-item-price">{{ number_format($item['price_byn'], 2, ',', ' ') }} BYN</div>
        </div>
        <div class="tf-mini-cart-price">
            <div class="wg-quantity style-2">
                <button type="button" class="btn-quantity minus-btn js-cart-qty" data-action="decrease">
                    <i class="icon icon-minus"></i>
                </button>
                <input class="quantity-product" type="text" value="{{ $item['qty'] }}" readonly>
                <button type="button" class="btn-quantity plus-btn js-cart-qty" data-action="increase">
                    <i class="icon icon-plus"></i>
                </button>
            </div>
            <div class="fw-semibold mt-8 js-cart-item-line">{{ number_format($item['line_byn'], 2, ',', ' ') }} BYN</div>
            <button type="button" class="tf-btn-line-3 type-primary mt-8 js-cart-remove">
                <span class="text-caption-01 fw-semibold">{{ __('Удалить') }}</span>
            </button>
        </div>
    </div>
@empty
    <div class="box-text_empty type-shop_cart">
        <div class="shop-empty_top">
            <span class="icon"><i class="icon-Handbag"></i></span>
            <h4 class="text-emp">{{ __('Ваш список для бронирования пуст') }}</h4>
            <p class="cl-text-2">{{ __('Добавьте товары в список для бронирования, чтобы отправить заявку') }}</p>
        </div>
        <div class="shop-empty_bot">
            <a href="{{ route('categories.index') }}" class="tf-btn animate-btn">{{ __('Перейти в каталог') }}</a>
        </div>
    </div>
@endforelse
