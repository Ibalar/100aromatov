@extends('layouts.app')

@section('title', __('Просмотренные товары') . ' - ' . config('app.name'))

@section('content')
    <x-breadcrumbs
        :title="__('Просмотренные товары')"
        :items="[
            ['title' => __('Просмотренные товары')]
        ]"
    />

    <section class="flat-spacing pt-0">
        <div class="container">
            @if($products->count() > 0)
                <div class="mb-4">
                    <p class="text-caption-01">{{ __('Просмотрено') }}: {{ $products->total() }}</p>
                </div>
                <div class="wrapper-shop tf-grid-layout tf-col-3">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                @if($products->hasPages())
                    <div class="wd-full justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="empty-products text-center py-5">
                    <h4>{{ __('Нет просмотренных товаров') }}</h4>
                    <p>{{ __('Вы ещё не смотрели товары.') }}</p>
                    <a href="{{ route('categories.index') }}" class="tf-btn btn-fill mt-3">
                        {{ __('Перейти в каталог') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
