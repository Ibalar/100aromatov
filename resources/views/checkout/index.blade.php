@extends('layouts.app')

@section('title', __('Оформление брони') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Оформление брони')"
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            ['title' => __('Список для бронирования'), 'url' => route('cart.index')],
            ['title' => __('Оформление брони')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            @if(session('success_order_id'))
                <div class="alert alert-success mb-24">
                    {{ __('Заявка на бронирование успешно отправлена') }} #{{ session('success_order_id') }}
                </div>
            @endif

            @if($errors->has('cart'))
                <div class="alert alert-danger mb-24">{{ $errors->first('cart') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-7">
                    <div class="checkout-box">
                        <h4 class="mb-16">{{ __('Контактные данные') }}</h4>
                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf
                            <input type="hidden" name="website" value="">
                            <input type="hidden" name="form_started_at" value="{{ now()->timestamp }}">
                            <div class="form-content">
                                <fieldset class="tf-field mb-12">
                                    <label class="tf-lable fw-medium">{{ __('Телефон') }} *</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" data-phone-by required>
                                    @error('phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </fieldset>
                                <fieldset class="tf-field mb-12">
                                    <label class="tf-lable fw-medium">{{ __('Перезвонить?') }}</label>
                                    <select name="call_preference" required>
                                        <option value="call_me" @selected(old('call_preference', 'call_me') === 'call_me')>{{ __('Перезвоните') }}</option>
                                        <option value="no_call" @selected(old('call_preference') === 'no_call')>{{ __('Перезванивать не нужно') }}</option>
                                    </select>
                                </fieldset>
                                <fieldset class="tf-field mb-12">
                                    <label class="tf-lable fw-medium">{{ __('Email') }}</label>
                                    <input type="email" name="email" value="{{ old('email') }}">
                                </fieldset>
                                <fieldset class="tf-field mb-16">
                                    <label class="tf-lable fw-medium">{{ __('Промокод') }}</label>
                                    <input type="text" name="promo_code" value="{{ old('promo_code') }}">
                                </fieldset>
                                <fieldset class="mb-16">
                                    <label class="d-flex align-items-start gap-8">
                                        <input type="checkbox" name="privacy_policy" value="1" @checked(old('privacy_policy')) required>
                                        <span>Я согласен(на) с <a href="/pages/privacy-policy" target="_blank" rel="noopener noreferrer">политикой конфиденциальности</a></span>
                                    </label>
                                    @error('privacy_policy')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </fieldset>
                            </div>
                            <button type="submit" class="tf-btn animate-btn">{{ __('Подтвердить бронь') }}</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="checkout-box">
                        <h4 class="mb-16">{{ __('Ваш список для бронирования') }}</h4>
                        <div id="js-checkout-items">
                            @include('partials.cart.items', ['items' => $items])
                        </div>
                        <div class="d-flex justify-content-between mt-16">
                            <span>{{ __('Товаров') }}: {{ $totalQty }}</span>
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
