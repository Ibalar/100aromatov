@extends('layouts.app')

@section('title', __('РћС„РѕСЂРјР»РµРЅРёРµ Р·Р°РєР°Р·Р°') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('РћС„РѕСЂРјР»РµРЅРёРµ Р·Р°РєР°Р·Р°')"
        :items="[
            ['title' => __('РљР°С‚Р°Р»РѕРі'), 'url' => route('categories.index')],
            ['title' => __('РљРѕСЂР·РёРЅР°'), 'url' => route('cart.index')],
            ['title' => __('РћС„РѕСЂРјР»РµРЅРёРµ Р·Р°РєР°Р·Р°')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            @if(session('success_order_id'))
                <div class="alert alert-success mb-24">
                    {{ __('Р—Р°РєР°Р· СѓСЃРїРµС€РЅРѕ РѕС„РѕСЂРјР»РµРЅ') }} #{{ session('success_order_id') }}
                </div>
            @endif

            @if($errors->has('cart'))
                <div class="alert alert-danger mb-24">{{ $errors->first('cart') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-7">
                    <div class="checkout-box">
                        <h4 class="mb-16">{{ __('РљРѕРЅС‚Р°РєС‚РЅС‹Рµ РґР°РЅРЅС‹Рµ') }}</h4>
                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf
                            <div class="form-content">
                                <fieldset class="tf-field mb-12">
                                    <label class="tf-lable fw-medium">{{ __('РўРµР»РµС„РѕРЅ') }} *</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                                </fieldset>
                                <fieldset class="tf-field mb-12">
                                    <label class="tf-lable fw-medium">{{ __('Email') }}</label>
                                    <input type="email" name="email" value="{{ old('email') }}">
                                </fieldset>
                                <fieldset class="tf-field mb-16">
                                    <label class="tf-lable fw-medium">{{ __('РџСЂРѕРјРѕРєРѕРґ') }}</label>
                                    <input type="text" name="promo_code" value="{{ old('promo_code') }}">
                                </fieldset>
                            </div>
                            <button type="submit" class="tf-btn animate-btn">{{ __('РџРѕРґС‚РІРµСЂРґРёС‚СЊ Р·Р°РєР°Р·') }}</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-box">
                        <h4 class="mb-16">{{ __('Р’Р°С€ Р·Р°РєР°Р·') }}</h4>
                        <div id="js-checkout-items">
                            @include('partials.cart.items', ['items' => $items])
                        </div>
                        <div class="d-flex justify-content-between mt-16">
                            <span>{{ __('РўРѕРІР°СЂРѕРІ') }}: {{ $totalQty }}</span>
                            <strong id="js-checkout-total">{{ number_format($totalByn, 2, ',', ' ') }} BYN</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .checkout-box {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
@endpush


