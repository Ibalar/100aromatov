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
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
                    <p class="text-caption-01 mb-0">{{ __('Просмотрено') }}: {{ $products->total() }}</p>

                    <div class="d-flex align-items-center gap-3">
                        <div class="tf-control-sorting">
                            <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                @php
                                    $sortOptions = [
                                        'best-selling' => __('По популярности'),
                                        'a-z' => __('А-Я'),
                                        'z-a' => __('Я-А'),
                                        'price-low-high' => __('Цена: по возрастанию'),
                                        'price-high-low' => __('Цена: по убыванию'),
                                    ];
                                    $currentSort = $sort ?? request('sort', 'best-selling');
                                @endphp
                                <div class="btn-select">
                                    <span class="text-sort-value">{{ $sortOptions[$currentSort] ?? __('По популярности') }}</span>
                                    <span class="icon icon-CaretDown"></span>
                                </div>
                                <div class="dropdown-menu">
                                    @foreach($sortOptions as $sortValue => $sortLabel)
                                        <div class="select-item {{ $currentSort === $sortValue ? 'active' : '' }}" data-sort-value="{{ $sortValue }}">
                                            <span class="text-value-item">{{ $sortLabel }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <ul class="tf-control-layout">
                            <li class="tf-view-layout-switch sw-layout-list list-layout" data-value-layout="list" title="{{ __('Список') }}">
                                <i class="icon-List"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-2" data-value-layout="tf-col-2" title="2 {{ __('колонки') }}">
                                <i class="icon-grid-2"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-3 active d-none d-md-flex" data-value-layout="tf-col-3" title="3 {{ __('колонки') }}">
                                <i class="icon-grid-3"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-4 d-none d-lg-flex" data-value-layout="tf-col-4" title="4 {{ __('колонки') }}">
                                <i class="icon-grid-4"></i>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tf-list-layout wrapper-shop" id="listLayout" style="display: none;">
                    @foreach($products as $product)
                        @include('components.product-card-list', ['product' => $product])
                    @endforeach
                    @if($products->hasPages())
                        <div class="wd-full justify-content-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>

                <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                    @if($products->hasPages())
                        <div class="wd-full justify-content-center">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
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

    @push('scripts')
    <script>
        document.querySelectorAll('.tf-dropdown-sort .select-item').forEach(item => {
            item.addEventListener('click', function () {
                const sortValue = this.dataset.sortValue || 'best-selling';
                const url = new URL(window.location.href);
                url.searchParams.set('sort', sortValue);
                window.location.href = url.toString();
            });
        });
    </script>
    @endpush
@endsection
