<div class="offcanvas offcanvas-end popup-shopping-cart" id="shoppingCart">
    <div class="canvas-wrapper">
        <div class="popup-header">
            <div class="d-flex align-items-center justify-content-between mb-12">
                <h5 class="title">{{ __('Список для бронирования') }}</h5>
                <span class="icon-X2 icon-close-popup" data-bs-dismiss="offcanvas"></span>
            </div>
        </div>
        <div class="wrap">
            <div class="tf-mini-cart-wrap">
                <div class="tf-mini-cart-main">
                    <div class="tf-mini-cart-sroll">
                        <div class="tf-mini-cart-items" id="js-cart-modal-items">
                            @include('partials.cart.items', ['items' => collect()])
                        </div>
                    </div>
                </div>
                <div class="tf-mini-cart-bottom">
                    <div class="tf-mini-cart-bottom-wrap">
                        <div class="tf-mini-cart-total">
                            <h5 class="text-total d-flex align-content-center justify-content-between">
                                <span class="subtotal">{{ __('Итого') }}</span>
                                <span class="total-price tf-totals-total-value" id="js-cart-modal-total">0,00 BYN</span>
                            </h5>
                        </div>
                        <div class="tf-mini-cart-view-checkout">
                            <a href="{{ route('cart.index') }}" class="tf-btn btn-stroke">{{ __('Весь список') }}</a>
                            <a href="{{ route('checkout.index') }}" class="tf-btn animate-btn text-center">{{ __('Оформить бронь') }}</a>
                        </div>
                        <button type="button" class="d-flex justify-content-center fw-semibold text-center link js-cart-clear">
                            {{ __('Очистить список') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
