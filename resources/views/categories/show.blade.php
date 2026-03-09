@extends('layouts.app')

@section('title', localizedField($category, 'name') . ' - ' . config('app.name'))
@section('meta_description', localizedField($category, 'seo_description') ?: localizedField($category, 'description') ?: __('Парфюмерия в категории') . ' ' . localizedField($category, 'name'))

@push('schema_org')
    <x-schema-org
        type="category"
        :title="localizedField($category, 'name') . ' - ' . config('app.name')"
        :description="localizedField($category, 'seo_description') ?: localizedField($category, 'description')"
        :category="$category"
    />
@endpush

@section('content')

    <x-breadcrumbs
        :title="localizedField($category, 'name')"
        :items="[
            ['title' => __('Категории'), 'url' => route('categories.index')],
            ['title' => localizedField($category, 'name')]
        ]"
    />

    <div class="flat-spacing">
        <div class="container">
            <div class="row">
                <!-- Sidebar with filters -->
                <div class="col-xl-3">
                    <div class="canvas-sidebar sidebar-filter canvas-filter left">
                        <div class="canvas-wrapper">
                            <div class="canvas-header">
                                <h4 class="title d-none d-xl-block">{{ __('Фильтры') }}</h4>
                                <h5 class="title d-xl-none">{{ __('Фильтры') }}</h5>
                                <span class="icon-X2 fs-24 close-filter d-xl-none"></span>
                            </div>
                            <div class="canvas-body">
                                <form method="GET" action="{{ route('category.show', $category->slug) }}" class="filter-form">
                                    @include('components.price-filter', ['priceRange' => $priceRange, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice])

                                    @include('components.attribute-filter', ['attributes' => $filterableAttributes, 'selectedAttributes' => $attributeFilters])

                                    <div class="filter-actions d-xl-none">
                                        <button type="submit" class="tf-btn btn-fill w-100">
                                            <span class="btn-text">{{ __('Применить') }}</span>
                                        </button>
                                        <a href="{{ route('category.show', $category->slug) }}" class="tf-btn btn-white w-100 mt-2">
                                            <span class="btn-text">{{ __('Сбросить') }}</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-xl-9">
                    <div class="tf-shop-control">
                        <button type="button" id="filterShop" class="tf-btn-filter d-xl-none">
                            <span class="icon icon-filter"></span>
                            <span class="text">{{ __('Показать все фильтры') }}</span>
                        </button>
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
                                    $currentSort = request('sort', 'best-selling');
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
                    <div class="wrapper-control-shop gridLayout-wrapper">
                        <div class="meta-filter-shop">
                            <div id="product-count-grid" class="count-text text-caption-01">
                                {{ __('Найдено') }}: {{ $products->total() }} {{ trans_choice('товар|товара|товаров', $products->total()) }}
                            </div>
                            <div id="product-count-list" class="count-text text-caption-01"></div>
                            <div class="br-line type-vertical"></div>
                            <div id="applied-filters"></div>
                            <button id="remove-all" class="remove-all-filters" style="display: none;">
                                <i class="icon icon-X2"></i>
                                {{ __('Сбросить все') }}
                            </button>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div class="tf-grid-layout wrapper-shop tf-col-3" id="gridLayout">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                @include('components.product-card', ['product' => $product])
                            @endforeach
                        @endif
                    </div>

                    <!-- List View -->
                    <div class="tf-list-layout wrapper-shop" id="listLayout" style="display: none;">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                @include('components.product-card', ['product' => $product])
                            @endforeach
                        @endif
                    </div>

                    @if($products->count() > 0)
                        <div class="pagination-wrapper">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="empty-products">
                            <div class="empty-icon">
                                <i class="icon icon-MagnifyingGlass"></i>
                            </div>
                            <h4>{{ __('Товары не найдены') }}</h4>
                            <p>{{ __('Попробуйте изменить параметры фильтрации') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection



