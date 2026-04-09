@extends('layouts.app')

@section('title', __('РР·Р±СЂР°РЅРЅРѕРµ') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('РР·Р±СЂР°РЅРЅРѕРµ')"
        :items="[
            ['title' => __('РР·Р±СЂР°РЅРЅРѕРµ')]
        ]"
    />

    <div class="section-wishlist flat-spacing pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h4 class="mb-0">{{ __('РЎРїРёСЃРѕРє РёР·Р±СЂР°РЅРЅРѕРіРѕ') }}</h4>
                <button
                    type="button"
                    class="tf-btn btn-stroke js-wishlist-clear {{ count($products) ? '' : 'd-none' }}"
                    id="js-wishlist-clear"
                >
                    {{ __('РћС‡РёСЃС‚РёС‚СЊ') }}
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
                <p class="text-notice cl-text-2 mb-20">{{ __('Р’С‹ РїРѕРєР° РЅРµ РґРѕР±Р°РІРёР»Рё С‚РѕРІР°СЂС‹ РІ РёР·Р±СЂР°РЅРЅРѕРµ.') }}</p>
                <a href="{{ route('categories.index') }}" class="tf-btn animate-btn">{{ __('РџРµСЂРµР№С‚Рё РІ РєР°С‚Р°Р»РѕРі') }}</a>
            </div>
        </div>
    </div>
@endsection

