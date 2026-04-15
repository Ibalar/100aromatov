@extends('layouts.app')

@section('title', localizedField($brand, 'seo_title') ?: $brand->name . ' - ' . config('app.name'))
@section('meta_description', localizedField($brand, 'seo_description') ?: $brand->name)

@push('styles')
    <x-seo-meta
        :title="localizedField($brand, 'seo_title') ?: $brand->name"
        :description="localizedField($brand, 'seo_description') ?: $brand->name"
        :image="$brand->logo ? asset('storage/' . $brand->logo) : null"
        :type="'brand'"
    />
@endpush

@push('schema_org')
    <x-schema-org
        type="brand"
        :entity="$brand"
    />
    <x-schema-org
        type="brand_products"
        :entity="$brand"
        :title="$brand->name"
        :description="localizedField($brand, 'seo_description') ?: $brand->name"
        :products="$products"
    />
@endpush

@section('content')
    @php
        $brandName = localizedField($brand, 'name');
        $brandDescription = localizedField($brand, 'description');
        $brandsLabel = "\u{0411}\u{0440}\u{0435}\u{043D}\u{0434}\u{044B}";
        $filtersLabel = "\u{0424}\u{0438}\u{043B}\u{044C}\u{0442}\u{0440}\u{044B}";
        $categoriesLabel = "\u{041A}\u{0430}\u{0442}\u{0435}\u{0433}\u{043E}\u{0440}\u{0438}\u{0438}";
        $applyLabel = "\u{041F}\u{0440}\u{0438}\u{043C}\u{0435}\u{043D}\u{0438}\u{0442}\u{044C}";
        $resetLabel = "\u{0421}\u{0431}\u{0440}\u{043E}\u{0441}\u{0438}\u{0442}\u{044C}";
        $showFiltersLabel = "\u{041F}\u{043E}\u{043A}\u{0430}\u{0437}\u{0430}\u{0442}\u{044C} \u{0432}\u{0441}\u{0435} \u{0444}\u{0438}\u{043B}\u{044C}\u{0442}\u{0440}\u{044B}";
        $sortPopularLabel = "\u{041F}\u{043E} \u{043F}\u{043E}\u{043F}\u{0443}\u{043B}\u{044F}\u{0440}\u{043D}\u{043E}\u{0441}\u{0442}\u{0438}";
        $sortAzLabel = "\u{0410}-\u{042F}";
        $sortZaLabel = "\u{042F}-\u{0410}";
        $sortPriceAscLabel = "\u{0426}\u{0435}\u{043D}\u{0430}: \u{043F}\u{043E} \u{0432}\u{043E}\u{0437}\u{0440}\u{0430}\u{0441}\u{0442}\u{0430}\u{043D}\u{0438}\u{044E}";
        $sortPriceDescLabel = "\u{0426}\u{0435}\u{043D}\u{0430}: \u{043F}\u{043E} \u{0443}\u{0431}\u{044B}\u{0432}\u{0430}\u{043D}\u{0438}\u{044E}";
        $listLabel = "\u{0421}\u{043F}\u{0438}\u{0441}\u{043E}\u{043A}";
        $columnsLabel = "\u{043A}\u{043E}\u{043B}\u{043E}\u{043D}\u{043A}\u{0438}";
        $resetAllLabel = "\u{0421}\u{0431}\u{0440}\u{043E}\u{0441}\u{0438}\u{0442}\u{044C} \u{0432}\u{0441}\u{0435}";
        $emptyTitle = "\u{0422}\u{043E}\u{0432}\u{0430}\u{0440}\u{044B} \u{043D}\u{0435} \u{043D}\u{0430}\u{0439}\u{0434}\u{0435}\u{043D}\u{044B}";
        $emptyText = "\u{041F}\u{043E}\u{043F}\u{0440}\u{043E}\u{0431}\u{0443}\u{0439}\u{0442}\u{0435} \u{0438}\u{0437}\u{043C}\u{0435}\u{043D}\u{0438}\u{0442}\u{044C} \u{043F}\u{0430}\u{0440}\u{0430}\u{043C}\u{0435}\u{0442}\u{0440}\u{044B} \u{0444}\u{0438}\u{043B}\u{044C}\u{0442}\u{0440}\u{0430}\u{0446}\u{0438}\u{0438}";
    @endphp

    <x-breadcrumbs
        :title="$brandName"
        :items="[
            ['title' => $brandsLabel, 'url' => route('brands.index')],
            ['title' => $brandName]
        ]"
    />

    <section class="flat-spacing-3 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="brand-info d-flex flex-column align-items-center text-center">
                        <h1>{{ localizedField($brand, 'h1_title') ?: $brand->name }}</h1>

                        @if($brand->logo)
                            <div class="brand-logo mb-4">
                                <img
                                    src="{{ asset('storage/' . $brand->logo) }}"
                                    alt="{{ $brandName }}"
                                    class="img-fluid"
                                    style="max-height: 120px;"
                                >
                            </div>
                        @endif

                        @if($brandDescription)
                            <div class="brand-description text-body-1 cl-text-2 mb-4" style="max-width: 800px;">
                                {!! $brandDescription !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="flat-spacing">
        <div class="container">
            <div class="row">
                <div class="col-xl-3">
                    <div class="canvas-sidebar sidebar-filter canvas-filter left">
                        <div class="canvas-wrapper">
                            <div class="canvas-header">
                                <h4 class="title d-none d-xl-block">{{ $filtersLabel }}</h4>
                                <h5 class="title d-xl-none">{{ $filtersLabel }}</h5>
                                <span class="icon-X2 fs-24 close-filter d-xl-none"></span>
                            </div>

                            <x-applied-filter-tags
                                :clear-url="route('brand.show', $brand->slug)"
                                :brands="collect()"
                                :filter-attributes="$filterableAttributes"
                                :selected-attributes="$attributeFilters"
                                :brand-filter="null"
                                :min-price="$minPrice"
                                :max-price="$maxPrice"
                                :price-range="$priceRange"
                            />

                            <div class="canvas-body">
                                <form method="GET" action="{{ route('brand.show', $brand->slug) }}" class="filter-form">
                                    <input type="hidden" name="sort" value="{{ $sort ?? request('sort', 'best-selling') }}">

                                    @if($categories->isNotEmpty())
                                        <div class="widget-facet">
                                            <div class="facet-title" data-bs-target="#filter-category" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="filter-category">
                                                <h6>{{ $categoriesLabel }}</h6>
                                                <span class="icon icon-CaretDown"></span>
                                            </div>
                                            <div id="filter-category" class="collapse show">
                                                <ul class="collapse-body filter-group-check">
                                                    @foreach($categories as $category)
                                                        <li class="list-item">
                                                            <input
                                                                type="checkbox"
                                                                name="category[]"
                                                                class="tf-check style-2"
                                                                id="category_{{ $category->id }}"
                                                                value="{{ $category->id }}"
                                                                {{ in_array((string) $category->id, array_map('strval', $categoryFilter ?? []), true) ? 'checked' : '' }}
                                                            >
                                                            <label for="category_{{ $category->id }}" class="label">
                                                                <span class="cate-text">{{ localizedField($category, 'name') }}</span>
                                                                <span class="count">({{ $category->products_count }})</span>
                                                            </label>
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
                                            <span class="btn-text">{{ $applyLabel }}</span>
                                        </button>
                                        <a href="{{ route('brand.show', $brand->slug) }}" class="tf-btn btn-white w-100 mt-2">
                                            <span class="btn-text">{{ $resetLabel }}</span>
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
                            <span class="text">{{ $showFiltersLabel }}</span>
                        </button>

                        <div class="tf-control-sorting">
                            <div class="tf-dropdown-sort" data-bs-toggle="dropdown">
                                @php
                                    $sortOptions = [
                                        'best-selling' => $sortPopularLabel,
                                        'a-z' => $sortAzLabel,
                                        'z-a' => $sortZaLabel,
                                        'price-low-high' => $sortPriceAscLabel,
                                        'price-high-low' => $sortPriceDescLabel,
                                    ];
                                    $currentSort = $sort ?? request('sort', 'best-selling');
                                @endphp
                                <div class="btn-select">
                                    <span class="text-sort-value">{{ $sortOptions[$currentSort] ?? $sortPopularLabel }}</span>
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
                            <li class="tf-view-layout-switch sw-layout-list list-layout" data-value-layout="list" title="{{ $listLabel }}">
                                <i class="icon-List"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-2" data-value-layout="tf-col-2" title="2 {{ $columnsLabel }}">
                                <i class="icon-grid-2"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-3 active d-none d-md-flex" data-value-layout="tf-col-3" title="3 {{ $columnsLabel }}">
                                <i class="icon-grid-3"></i>
                            </li>
                            <li class="tf-view-layout-switch sw-layout-4 d-none d-lg-flex" data-value-layout="tf-col-4" title="4 {{ $columnsLabel }}">
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
                                {{ $resetAllLabel }}
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
                            <h4>{{ $emptyTitle }}</h4>
                            <p>{{ $emptyText }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

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
                    window.location.href = '{{ route('brand.show', $brand->slug) }}';
                });
            }
        })();
    </script>
@endpush
