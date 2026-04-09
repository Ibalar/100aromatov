@extends('layouts.app')

@section('title', ($searchQuery !== '' ? $searchQuery . ' - ' : '') . __('Поиск') . ' - ' . config('app.name'))
@section('meta_description', __('Результаты поиска по каталогу товаров'))

@section('content')
    <x-breadcrumbs
        :title="__('Поиск')"
        :items="[
            ['title' => __('Поиск')]
        ]"
    />

    <section class="flat-spacing pt-0">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <form action="{{ route('search') }}" method="GET" class="form-search-nav style-3 mb-4">
                        <fieldset>
                            <input type="text" name="q" value="{{ $searchQuery }}" placeholder="{{ __('Искать по названию, бренду или артикулу') }}" required>
                        </fieldset>
                        <button type="submit" class="btn-action">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </button>
                    </form>

                    @if($searchQuery === '')
                        <div class="empty-products text-center py-5">
                            <div class="empty-icon mb-3">
                                <i class="icon icon-MagnifyingGlass"></i>
                            </div>
                            <h4>{{ __('Введите поисковый запрос') }}</h4>
                            <p>{{ __('Например: бренд, название аромата или артикул') }}</p>
                        </div>
                    @elseif($products->count() === 0)
                        <div class="empty-products text-center py-5">
                            <div class="empty-icon mb-3">
                                <i class="icon icon-MagnifyingGlass"></i>
                            </div>
                            <h4>{{ __('Ничего не найдено') }}</h4>
                            <p>{{ __('По запросу ":query" товары не найдены.', ['query' => $searchQuery]) }}</p>
                        </div>
                    @else
                        <div class="mb-4">
                            <h3 class="mb-2">{{ __('Результаты поиска') }}</h3>
                            <p class="text-caption-01">{{ __('Запрос') }}: "{{ $searchQuery }}". {{ __('Найдено на этой странице') }}: {{ $products->count() }}</p>
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
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
