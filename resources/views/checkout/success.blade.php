@extends('layouts.app')

@section('title', __('Заказ оформлен!') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Заказ оформлен!')"
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            ['title' => __('Список для бронирования'), 'url' => route('cart.index')],
            ['title' => __('Заказ оформлен!')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="checkout-box text-center">
                        <div class="order-success-icon">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <h1 class="mb-12">{{ __('Заказ оформлен!') }}</h1>

                        <p class="text-caption-01 cl-text-3 mb-24">
                            {{ __('Спасибо! Ваш заказ принят в обработку.') }}
                        </p>

                        <div class="order-success-details text-start">
                            <h2 class="mb-16">
                                {{ __('Заказ №:id', ['id' => $order->id]) }}
                            </h2>

                            <div class="order-success-row">
                                <span class="cl-text-3">{{ __('Статус') }}</span>
                                <span class="fw-medium">{{ __('Новый') }}</span>
                            </div>

                            <div class="order-success-row">
                                <span class="cl-text-3">{{ __('Телефон') }}</span>
                                <span class="fw-medium">{{ $order->phone }}</span>
                            </div>

                            @if ($order->call_preference === 'no_call')
                                <div class="order-success-row">
                                    <span class="cl-text-3">{{ __('Перезвон') }}</span>
                                    <span class="fw-medium">{{ __('Перезванивать не нужно') }}</span>
                                </div>
                            @endif

                            @if ($order->email)
                                <div class="order-success-row">
                                    <span class="cl-text-3">Email</span>
                                    <span class="fw-medium">{{ $order->email }}</span>
                                </div>
                            @endif

                            @if ($order->items->isNotEmpty())
                                <div class="order-success-items">
                                    <div class="text-caption-01 cl-text-3 mb-12">{{ __('Состав заказа') }}</div>
                                    @foreach ($order->items as $item)
                                        <div class="order-success-row">
                                            <span>
                                                {{ $item->name_snapshot }}
                                                @if ($item->volume_ml_snapshot)
                                                    <span class="cl-text-3">({{ $item->volume_ml_snapshot }} ml)</span>
                                                @endif
                                                <span class="cl-text-3">x{{ $item->qty }}</span>
                                            </span>
                                            <span class="fw-medium">
                                                {{ number_format((float) $item->price_byn_snapshot * $item->qty, 2, ',', ' ') }} BYN
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="order-success-total">
                                <div class="order-success-row order-success-total-row">
                                    <span>{{ __('Итого') }}</span>
                                    <span>{{ number_format((float) $order->total_byn, 2, ',', ' ') }} BYN</span>
                                </div>
                                @if ($order->discount_usd > 0)
                                    <div class="order-success-row text-success mt-8">
                                        <span>{{ __('Скидка') }}</span>
                                        <span>-{{ number_format($settings->convertUsdToByn((float) $order->discount_usd), 2, ',', ' ') }} BYN</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-caption-01 cl-text-3 mt-24 mb-16">
                            {{ __('Мы свяжемся с вами для подтверждения заказа.') }}
                        </p>
                        <a href="{{ route('home') }}" class="tf-btn animate-btn">
                            {{ __('Вернуться в магазин') }}
                        </a>
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
        .order-success-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
            color: #1f9d55;
        }
        .order-success-icon svg {
            width: 80px;
            height: 80px;
            max-width: 80px;
            flex-shrink: 0;
        }
        .order-success-details {
            background: #f8f8f8;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 8px;
        }
        .order-success-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 6px 0;
        }
        .order-success-items {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #e5e5e5;
        }
        .order-success-total {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #e5e5e5;
        }
        .order-success-total-row {
            font-size: 18px;
            font-weight: 700;
        }
        .text-success {
            color: #1f9d55;
        }
    </style>
@endpush
