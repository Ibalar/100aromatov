<!-- Toolbar -->
<div class="tf-toolbar-bottom">
    <div class="toolbar-item">
        <a href="{{ route('categories.index') }}">
                <span class="toolbar-icon">
                    <i class="icon icon-storefront"></i>
                </span>
            <span class="toolbar-label">Shop</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="{{ route('search') }}">
                <span class="toolbar-icon">
                    <i class="icon icon-MagnifyingGlass"></i>
                </span>
            <span class="toolbar-label">Search</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="{{ auth('customer')->check() ? route('customer.account.dashboard') : route('customer.login') }}">
                <span class="toolbar-icon">
                    <i class="icon icon-User"></i>
                </span>
            <span class="toolbar-label">Account</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="{{ route('wishlist.index') }}" class="js-wishlist-link">
                <span class="toolbar-icon">
                    <i class="icon {{ ($wishlistCount ?? 0) > 0 ? 'icon-heart' : 'icon-HeartStraight' }}"></i>
                </span>
            <span class="toolbar-label">Wishlist</span>
        </a>
    </div>
    <div class="toolbar-item">
        <a href="{{ route('cart.index') }}">
                <span class="toolbar-icon">
                    <i class="icon icon-Handbag"></i>
                    <span class="toolbar-count js-cart-count">{{ $cartCount ?? 0 }}</span>
                </span>
            <span class="toolbar-label">Cart</span>
        </a>
    </div>
</div>
<!-- /Toolbar -->

