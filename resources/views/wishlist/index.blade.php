@extends('layouts.app')

@section('title', __('Избранное') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Избранное')"
        :items="[
            ['title' => __('Избранное')]
        ]"
    />

    <div class="section-wishlist flat-spacing pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h4 class="mb-0">{{ __('Список избранного') }}</h4>
                <button
                    type="button"
                    class="tf-btn btn-stroke js-wishlist-clear {{ count($products) ? '' : 'd-none' }}"
                    id="js-wishlist-clear"
                >
                    {{ __('Очистить') }}
                </button>
            </div>

            <div class="tf-grid-layout tf-col-2 md-col-3 xl-col-4 wrapper-wishlist" id="js-wishlist-grid">
                @forelse($products as $product)
                    <div class="wishlist-item" data-product-id="{{ $product->id }}">
                        @include('components.product-card', ['product' => $product, 'wishlistMode' => true])
                    </div>
                @empty
                @endforelse
            </div>

            <div class="tf-wishlist-empty text-center {{ count($products) ? 'd-none' : '' }}" id="js-wishlist-empty">
                <p class="text-notice cl-text-2 mb-20">{{ __('Вы пока не добавили товары в избранное.') }}</p>
                <a href="{{ route('categories.index') }}" class="tf-btn animate-btn">{{ __('Перейти в каталог') }}</a>
            </div>
        </div>
    </div>
@endsection
