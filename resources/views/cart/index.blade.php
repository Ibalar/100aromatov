@extends('layouts.app')

@section('title', __('РљРѕСЂР·РёРЅР°') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('РљРѕСЂР·РёРЅР°')"
        :items="[
            ['title' => __('РљР°С‚Р°Р»РѕРі'), 'url' => route('categories.index')],
            ['title' => __('РљРѕСЂР·РёРЅР°')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h3 class="mb-0">{{ __('РљРѕСЂР·РёРЅР°') }}</h3>
                <button type="button" class="tf-btn btn-stroke js-cart-clear">{{ __('РћС‡РёСЃС‚РёС‚СЊ РєРѕСЂР·РёРЅСѓ') }}</button>
            </div>

            <div id="js-cart-page-items">
                @include('partials.cart.items', ['items' => $items])
            </div>

            <div class="d-flex justify-content-end mt-24">
                <div class="text-end">
                    <h5>{{ __('РС‚РѕРіРѕ') }}: <span id="js-cart-page-total">{{ number_format($totalByn, 2, ',', ' ') }} BYN</span></h5>
                    <a href="{{ route('checkout.index') }}" class="tf-btn animate-btn mt-12">{{ __('РџРµСЂРµР№С‚Рё Рє РѕС„РѕСЂРјР»РµРЅРёСЋ') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection


