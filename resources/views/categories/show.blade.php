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
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            ['title' => localizedField($category, 'name')]
        ]"
    />

    @if($childCategories->isNotEmpty())
        <section class="flat-spacing pb-0">
            <div class="container">
                <div dir="ltr" class="swiper tf-swiper" data-preview="6" data-tablet="4" data-mobile-sm="3"
                    data-mobile="2" data-space-lg="30" data-space-md="15" data-space="10" data-pagination="2"
                    data-pagination-sm="3" data-pagination-md="4" data-pagination-lg="6">
                    <div class="swiper-wrapper">
                        @foreach($childCategories as $childCategory)
                            <div class="swiper-slide">
                                <a href="{{ route('category.show', $childCategory->slug) }}" class="category-v01 hover-img">
                                    <div class="cate-image img-style d-flex align-items-center justify-content-center">
                                        @if($childCategory->image)
                                            <img
                                                loading="lazy"
                                                src="{{ asset('storage/' . $childCategory->image) }}"
                                                alt="{{ localizedField($childCategory, 'name') }}"
                                            >
                                        @else
                                            <div class="cate-thumb-placeholder text-center px-3">
                                                <span class="icon icon-shopping-cart-simple fs-36"></span>
                                            </div>
                                        @endif
                                    </div>
                                    <h5 class="cate-name text-center link link-underline">
                                        {{ localizedField($childCategory, 'name') }}
                                    </h5>
                                    <p class="text-caption-01 text-center text-secondary mb-0">
                                        {{ $childCategory->products_count }} {{ trans_choice('товар|товара|товаров', $childCategory->products_count) }}
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="sw-line-default style-2 tf-sw-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    <section class="flat-spacing">
        <div class="container">
            <div class="row">
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
                                    @if($sidebarCategories->isNotEmpty())
                                        <div class="widget-facet">
                                            <div class="facet-title" data-bs-target="#filter-category" role="button"
                                                data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-category">
                                                <h6>{{ __('Категории') }}</h6>
                                                <span class="icon icon-CaretDown"></span>
                                            </div>
                                            <div id="filter-category" class="collapse show">
                                                <ul class="collapse-body filter-group-check group-category">
                                                    @foreach($sidebarCategories as $sidebarCategory)
                                                        <li class="list-item">
                                                            <a href="{{ route('category.show', $sidebarCategory->slug) }}"
                                                               class="label link {{ $sidebarCategory->id === $category->id ? 'fw-semibold' : '' }}">
                                                                <span class="cate-text">{{ localizedField($sidebarCategory, 'name') }}</span>
                                                                <span class="count">({{ $sidebarCategory->products_count }})</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="br-line"></div>
                                    @endif

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
                            <div id="product-count-grid" class="count-text text-caption-01"></div>
                            <div id="product-count-list" class="count-text text-caption-01"></div>
                            <div class="br-line type-vertical"></div>
                            <div id="applied-filters"></div>
                            <button id="remove-all" class="remove-all-filters" style="display: none;">
                                <i class="icon icon-X2"></i>
                                {{ __('Сбросить все') }}
                            </button>
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
                    </div>

                    @if($products->count() === 0)
                        <div class="empty-products text-center py-5">
                            <div class="empty-icon mb-3">
                                <i class="icon icon-MagnifyingGlass"></i>
                            </div>
                            <h4>{{ __('Товары не найдены') }}</h4>
                            <p>{{ __('Попробуйте изменить параметры фильтрации') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .cate-thumb-placeholder {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background: linear-gradient(135deg, #f3f3f3 0%, #e7e0d7 100%);
            color: #181818;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush
