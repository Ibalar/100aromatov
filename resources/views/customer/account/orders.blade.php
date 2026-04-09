@extends('layouts.app')

@section('title', __('Мои заказы') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Мои заказы')"
        :items="[
            ['title' => __('Личный кабинет'), 'url' => route('customer.account.dashboard')],
            ['title' => __('Мои заказы')]
        ]"
    />

    <section class="flat-spacing-3 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('customer.account._nav')
                </div>
                <div class="col-lg-9">
                    <div class="card p-4">
                        <h3 class="mb-3">{{ __('История заказов') }}</h3>
                        @forelse($orders as $order)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>#{{ $order->id }}</strong>
                                    <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="mb-2">{{ __('Статус') }}: {{ $order->status }}</div>
                                <div class="mb-2">{{ __('Сумма') }}: {{ number_format((float)$order->total_byn, 2, ',', ' ') }} BYN</div>
                                @if($order->items->count())
                                    <div class="small cl-text-2">
                                        @foreach($order->items as $item)
                                            <div>{{ $item->name_snapshot }} ({{ $item->sku_snapshot }}) × {{ $item->qty }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="mb-0 cl-text-2">{{ __('У вас пока нет заказов') }}</p>
                        @endforelse

                        <div class="mt-3">{{ $orders->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

