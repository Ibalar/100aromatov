@extends('layouts.app')

@section('title', __('Каталог') . ' - ' . config('app.name'))
@section('meta_description', __('Полный каталог парфюмерии по категориям'))

@push('schema_org')
    <x-schema-org
        type="categories_list"
        :title="__('Каталог') . ' - ' . config('app.name')"
        :description="__('Полный каталог парфюмерии по категориям')"
    />
@endpush

@section('content')
    <x-breadcrumbs
        :title="__('Каталог')"
        :items="[
            ['title' => __('Каталог')]
        ]"
    />

    @if($rootCategories->isNotEmpty())
        <section class="flat-spacing pb-0 pt-0">
            <div class="container">
                <div class="tf-grid-layout ssm-col-3 xl-col-3 gap-lg-30 gap-15">
                    @foreach($rootCategories as $rootCategory)
                        <div class="category-v03 style-2 hover-img4">
                            <a href="{{ route('category.show', $rootCategory->slug) }}" class="cate-image img-style4 d-block">
                                @if($rootCategory->image)
                                    <img
                                        loading="lazy"
                                        width="330"
                                        height="330"
                                        src="{{ asset('storage/' . $rootCategory->image) }}"
                                        alt="{{ localizedField($rootCategory, 'name') }}"
                                    >
                                @else
                                    <div class="cate-thumb-placeholder cate-thumb-placeholder-v03">
                                        <span class="icon icon-shopping-cart-simple fs-36"></span>
                                    </div>
                                @endif
                            </a>
                            <div class="cate-content text-center">
                                <a href="{{ route('category.show', $rootCategory->slug) }}" class="cate_name h5 fw-medium">
                                    {{ localizedField($rootCategory, 'name') }}
                                    <i class="icon icon-ArrowUpRight1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
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
                                :clear-url="route('categories.index')"
                                :brands="$brands"
                                :filter-attributes="$filterableAttributes"
                                :selected-attributes="$attributeFilters"
                                :brand-filter="$brandFilter"
                                :min-price="$minPrice"
                                :max-price="$maxPrice"
                                :price-range="$priceRange"
                            />
                            <div class="canvas-body">
                                <form method="GET" action="{{ route('categories.index') }}" class="filter-form">
                                    <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'best-selling') }}">

                                    @if($rootCategories->isNotEmpty())
                                        <div class="widget-facet">
                                            <div class="facet-title" data-bs-target="#filter-category" role="button"
                                                data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-category">
                                                <h6>{{ __('Категории') }}</h6>
                                                <span class="icon icon-CaretDown"></span>
                                            </div>
                                            <div id="filter-category" class="collapse show">
                                                <ul class="collapse-body filter-group-check group-category">
                                                    @foreach($rootCategories as $rootCategory)
                                                        <li class="list-item">
                                                            <a href="{{ route('category.show', $rootCategory->slug) }}" class="label link">
                                                                <span class="cate-text">{{ localizedField($rootCategory, 'name') }}</span>
                                                                <span class="count">({{ $rootCategory->products_count }})</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="br-line"></div>
                                    @endif

                                    @include('components.price-filter', ['priceRange' => $priceRange, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice])

                                    <div class="widget-facet">
                                        <div class="facet-title" data-bs-target="#filter-brand" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-brand">
                                            <h6>{{ __('Бренд') }}</h6>
                                            <span class="icon icon-CaretDown"></span>
                                        </div>
                                        <div id="filter-brand" class="collapse show">
                                            <ul class="collapse-body filter-group-check">
                                                <li class="list-item">
                                                    <a href="{{ route('categories.index', request()->except(['brand', 'page'])) }}" class="label link">
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

                                    @include('components.attribute-filter', ['attributes' => $filterableAttributes, 'selectedAttributes' => $attributeFilters])

                                    <div class="filter-actions d-xl-none">
                                        <button type="submit" class="tf-btn btn-fill w-100">
                                            <span class="btn-text">{{ __('Применить') }}</span>
                                        </button>
                                        <a href="{{ route('categories.index') }}" class="tf-btn btn-white w-100 mt-2">
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

        .cate-thumb-placeholder-v03 {
            aspect-ratio: 330 / 440;
            border-radius: 0;
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
                    window.location.href = '{{ route('categories.index') }}';
                });
            }
        })();
    </script>
@endpush
