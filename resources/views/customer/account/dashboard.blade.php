@extends('layouts.app')

@section('title', __('Личный кабинет') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Личный кабинет')"
        :items="[
            ['title' => __('Личный кабинет')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('customer.account._nav')
                </div>
                <div class="col-lg-9">
                    <div class="card p-4 mb-4">
                        <h3 class="mb-2">{{ __('Здравствуйте') }}, {{ $customer->full_name }}</h3>
                        <p class="mb-0 cl-text-2">{{ __('Здесь вы можете управлять заказами и настройками профиля') }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <div class="cl-text-2">{{ __('Количество заказов') }}</div>
                                <h4 class="mb-0">{{ $ordersCount }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <div class="cl-text-2">{{ __('Сумма заказов') }}</div>
                                <h4 class="mb-0">{{ number_format($ordersTotal, 2, ',', ' ') }} BYN</h4>
                            </div>
                        </div>
                    </div>

                    <div class="card p-4">
                        <h4 class="mb-3">{{ __('Последние заказы') }}</h4>
                        @forelse($recentOrders as $order)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span>#{{ $order->id }} • {{ $order->created_at->format('d.m.Y H:i') }}</span>
                                <span>{{ number_format((float)$order->total_byn, 2, ',', ' ') }} BYN</span>
                            </div>
                        @empty
                            <p class="mb-0 cl-text-2">{{ __('Заказов пока нет') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

