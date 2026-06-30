@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto text-center">
        <div class="mb-8">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ __('Заказ оформлен!') }}
        </h1>

        <p class="text-lg text-gray-600 mb-8">
            {{ __('Спасибо! Ваш заказ принят в обработку.') }}
        </p>

        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                {{ __('Заказ №:id', ['id' => $order->id]) }}
            </h2>

            <div class="space-y-3 text-sm text-gray-700">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Статус') }}</span>
                    <span class="font-medium">{{ __('Новый') }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Телефон') }}</span>
                    <span class="font-medium">{{ $order->phone }}</span>
                </div>

                @if ($order->call_preference === 'no_call')
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Перезвон') }}</span>
                    <span class="font-medium text-orange-600">{{ __('Перезванивать не нужно') }}</span>
                </div>
                @endif

                @if ($order->email)
                <div class="flex justify-between">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium">{{ $order->email }}</span>
                </div>
                @endif
            </div>

            @if ($order->items->isNotEmpty())
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-3">{{ __('Состав заказа') }}</h3>
                <div class="space-y-2">
                    @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">
                            {{ $item->name_snapshot }}
                            @if ($item->volume_ml_snapshot)
                                <span class="text-gray-400">({{ $item->volume_ml_snapshot }} ml)</span>
                            @endif
                            <span class="text-gray-400">x{{ $item->qty }}</span>
                        </span>
                        <span class="font-medium">
                            {{ number_format((float) $item->price_byn_snapshot * $item->qty, 2, ',', ' ') }} BYN
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-between text-lg font-bold text-gray-900">
                    <span>{{ __('Итого') }}</span>
                    <span>{{ number_format((float) $order->total_byn, 2, ',', ' ') }} BYN</span>
                </div>
                @if ($order->discount_usd > 0)
                <div class="flex justify-between text-sm text-green-600 mt-1">
                    <span>{{ __('Скидка') }}</span>
                    <span>-{{ number_format($settings->convertUsdToByn((float) $order->discount_usd), 2, ',', ' ') }} BYN</span>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-3">
            <p class="text-sm text-gray-500">
                {{ __('Мы свяжемся с вами для подтверждения заказа.') }}
            </p>
            <a href="{{ route('home') }}"
               class="inline-block px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                {{ __('Вернуться в магазин') }}
            </a>
        </div>
    </div>
</div>
@endsection
