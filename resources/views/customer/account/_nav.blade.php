<div class="account-nav card p-3 mb-4">
    <a href="{{ route('customer.account.dashboard') }}" class="d-block mb-2 {{ request()->routeIs('customer.account.dashboard') ? 'fw-bold' : '' }}">
        {{ __('Панель') }}
    </a>
    <a href="{{ route('customer.account.orders') }}" class="d-block mb-2 {{ request()->routeIs('customer.account.orders') ? 'fw-bold' : '' }}">
        {{ __('Заказы') }}
    </a>
    <a href="{{ route('customer.account.profile') }}" class="d-block mb-2 {{ request()->routeIs('customer.account.profile') ? 'fw-bold' : '' }}">
        {{ __('Профиль') }}
    </a>
    <a href="{{ route('customer.account.addresses') }}" class="d-block mb-2 {{ request()->routeIs('customer.account.addresses') ? 'fw-bold' : '' }}">
        {{ __('Адреса') }}
    </a>
    <a href="{{ route('customer.account.security') }}" class="d-block mb-3 {{ request()->routeIs('customer.account.security') ? 'fw-bold' : '' }}">
        {{ __('Безопасность') }}
    </a>
    <form method="POST" action="{{ route('customer.logout') }}">
        @csrf
        <button class="tf-btn btn-stroke w-100" type="submit">{{ __('Выйти') }}</button>
    </form>
</div>

