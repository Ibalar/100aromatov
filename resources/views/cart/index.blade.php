@extends('layouts.app')

@section('title', __('Список для бронирования') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Список для бронирования')"
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            ['title' => __('Список для бронирования')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h3 class="mb-0">{{ __('Список для бронирования') }}</h3>
                <button type="button" class="tf-btn btn-stroke js-cart-clear">{{ __('Очистить список') }}</button>
            </div>

            <div id="js-cart-page-items">
                @include('partials.cart.items', ['items' => $items])
            </div>

            <div class="d-flex justify-content-end mt-24">
                <div class="text-end">
                    <h5>{{ __('Итого') }}: <span id="js-cart-page-total">{{ number_format($totalByn, 2, ',', ' ') }} BYN</span></h5>
                    <a href="{{ route('checkout.index') }}" class="tf-btn animate-btn mt-12">{{ __('Перейти к бронированию') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
