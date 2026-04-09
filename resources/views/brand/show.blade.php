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
    @endphp

    <x-breadcrumbs
        :title="$brandName"
        :items="[
            ['title' => __('Бренды'), 'url' => route('brands.index')],
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

    <section class="flat-spacing-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tf-shop-control">
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
                </div>
            </div>

            @if($products->count() > 0)
                <div class="wrapper-control-shop gridLayout-wrapper">
                    <div class="meta-filter-shop">
                        <div id="product-count-grid" class="count-text text-caption-01">
                            {{ $products->total() }} {{ trans_choice('товар|товара|товаров', $products->total()) }}
                        </div>
                        <div id="product-count-list" class="count-text text-caption-01">
                            {{ $products->total() }} {{ trans_choice('товар|товара|товаров', $products->total()) }}
                        </div>
                    </div>

                    <div class="tf-list-layout wrapper-shop" id="listLayout" style="display: none;">
                        @foreach($products as $product)
                            @include('components.product-card-list', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="wrapper-shop tf-grid-layout tf-col-3" id="gridLayout">
                        @foreach($products as $product)
                            @include('components.product-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>

                @if($products->hasPages())
                    <div class="wd-full justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="empty-products text-center py-5">
                    <div class="empty-icon mb-3">
                        <i class="icon icon-MagnifyingGlass"></i>
                    </div>
                    <h4>{{ __('Товары не найдены') }}</h4>
                    <p>{{ __('В данный момент нет доступных товаров этого бренда.') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
