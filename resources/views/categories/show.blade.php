@extends('layouts.app')

@php
    $activeFilterPage = $activeFilterPage ?? null;
    $filterTiles = $filterTiles ?? $category->filterPages()->where('show_in_category_tiles', true)->orderBy('h1_ru')->get();

    $baseCategoryUrl = route('category.show', $category->slug);
    $currentCategoryUrl = $activeFilterPage
        ? route('category.filter', ['slug' => $category->slug, 'filterSlug' => $activeFilterPage->slug])
        : $baseCategoryUrl;

    $baseRouteName = $activeFilterPage ? 'category.filter' : 'category.show';
    $baseRouteParams = $activeFilterPage
        ? ['slug' => $category->slug, 'filterSlug' => $activeFilterPage->slug]
        : ['slug' => $category->slug];

    $pageTitle = $activeFilterPage
        ? (localizedField($activeFilterPage, 'seo_title') ?: localizedField($activeFilterPage, 'h1') ?: localizedField($category, 'name'))
        : localizedField($category, 'name');

    $metaDescription = $activeFilterPage
        ? (localizedField($activeFilterPage, 'seo_description') ?: localizedField($activeFilterPage, 'seo_text'))
        : (localizedField($category, 'seo_description') ?: localizedField($category, 'description'));

    $h1Title = $activeFilterPage
        ? (localizedField($activeFilterPage, 'h1') ?: localizedField($category, 'name'))
        : localizedField($category, 'name');
@endphp

@section('title', $pageTitle . ' - ' . config('app.name'))
@section('meta_description', $metaDescription ?: __('Парфюмерия в категории') . ' ' . localizedField($category, 'name'))

@push('schema_org')
    <x-schema-org
        type="category"
        :title="$pageTitle . ' - ' . config('app.name')"
        :description="$metaDescription"
        :category="$category"
    />
@endpush

@section('content')
    <x-breadcrumbs
        :title="$h1Title"
        :items="[
            ['title' => __('Каталог'), 'url' => route('categories.index')],
            ['title' => $h1Title]
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

                            <x-applied-filter-tags
                                :clear-url="$currentCategoryUrl"
                                :brands="$brands"
                                :filter-attributes="$filterableAttributes"
                                :selected-attributes="$attributeFilters"
                                :brand-filter="$brandFilter"
                                :min-price="$minPrice"
                                :max-price="$maxPrice"
                                :price-range="$priceRange"
                            />

                            <div class="canvas-body">
                                <form method="GET" action="{{ $currentCategoryUrl }}" class="filter-form">
                                    <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'best-selling') }}">

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

                                    @if($brands->isNotEmpty())
                                        <div class="widget-facet">
                                            <div class="facet-title" data-bs-target="#filter-brand" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-brand">
                                                <h6>{{ __('Бренд') }}</h6>
                                                <span class="icon icon-CaretDown"></span>
                                            </div>
                                            <div id="filter-brand" class="collapse show">
                                                <ul class="collapse-body filter-group-check">
                                                    <li class="list-item">
                                                        <a href="{{ route($baseRouteName, array_merge($baseRouteParams, request()->except(['brand', 'page']))) }}" class="label link">
                                                            <span class="cate-text">{{ __('Все бренды') }}</span>
                                                        </a>
                                                    </li>
                                                    @foreach($brands as $brand)
                                                        <li class="list-item">
                                                            <input type="checkbox" name="brand[]" class="tf-check style-2" id="brand_{{ $brand->id }}" value="{{ $brand->id }}" {{ in_array((string) $brand->id, array_map('strval', $brandFilter ?? []), true) ? 'checked' : '' }}>
                                                            <label for="brand_{{ $brand->id }}" class="label">
                                                                <span class="cate-text">{{ $brand->name }}</span>
                                                                <span class="count">({{ $brand->products_count }})</span>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="br-line"></div>
                                    @endif

                                    @include('components.attribute-filter', ['attributes' => $filterableAttributes, 'selectedAttributes' => $attributeFilters])

                                    <div class="filter-actions d-xl-none">
                                        <button type="submit" class="tf-btn btn-fill w-100">
                                            <span class="btn-text">{{ __('Применить') }}</span>
                                        </button>
                                        <a href="{{ $currentCategoryUrl }}" class="tf-btn btn-white w-100 mt-2">
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

                    @if($filterTiles->isNotEmpty())
                        <div class="category-filter-tiles">
                            <a href="{{ $baseCategoryUrl }}" class="filter-tile {{ $activeFilterPage ? '' : 'active' }}">
                                {{ __('Все') }}
                            </a>
                            @foreach($filterTiles as $tile)
                                <a
                                    href="{{ route('category.filter', ['slug' => $category->slug, 'filterSlug' => $tile->slug]) }}"
                                    class="filter-tile {{ $activeFilterPage && $activeFilterPage->id === $tile->id ? 'active' : '' }}"
                                >
                                    {{ localizedField($tile, 'h1') ?: $tile->slug }}
                                </a>
                            @endforeach
                        </div>
                    @endif

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

        .category-filter-tiles {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 0 24px;
        }

        .category-filter-tiles .filter-tile {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 14px;
            border: 1px solid #dfd7cc;
            border-radius: 999px;
            font-size: 13px;
            line-height: 1;
            color: #181818;
            background: #fff;
            transition: all .2s ease;
        }

        .category-filter-tiles .filter-tile:hover {
            border-color: #181818;
        }

        .category-filter-tiles .filter-tile.active {
            color: #fff;
            border-color: #181818;
            background: #181818;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('.filter-form');
            if (!form) {
                return;
            }

            const submitForm = () => form.requestSubmit ? form.requestSubmit() : form.submit();

            form.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach((input) => {
                input.addEventListener('change', submitForm);
            });

            document.querySelectorAll('.tf-dropdown-sort .select-item').forEach((item) => {
                item.addEventListener('click', function () {
                    const sortInput = form.querySelector('input[name="sort"]');
                    if (!sortInput) {
                        return;
                    }

                    sortInput.value = this.dataset.sortValue || 'best-selling';
                    submitForm();
                });
            });

            const removeAllButton = document.getElementById('remove-all');
            if (removeAllButton) {
                removeAllButton.addEventListener('click', function () {
                    window.location.href = @json($currentCategoryUrl);
                });
            }
        })();
    </script>
@endpush
