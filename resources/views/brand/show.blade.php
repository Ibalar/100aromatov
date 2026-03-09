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
    @endphp

    <x-breadcrumbs
        :title="$brandName"
        :items="[
        ['title' => 'Бренды', 'url' => route('brands.index')],
        ['title' => $brandName]
    ]"
    />

    <!-- Brand Info -->
    <section class="flat-spacing-3 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="brand-info d-flex flex-column align-items-center text-center">
                        <h1>{{ localizedField($brand, 'h1_title') ?: $brand->name }}</h1>
                        @if($brand->logo)
                            <div class="brand-logo mb-4">
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brandName }}" class="img-fluid" style="max-height: 120px;">
                            </div>
                        @endif

                        @php
                            $brandDescription = localizedField($brand, 'description');
                        @endphp

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
    <!-- /Brand Info -->

    <!-- Products Grid -->
    <section class="flat-spacing-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Товары бренда</h4>
                        <span class="text-body-2 cl-text-2">{{ $products->total() }} товаров</span>
                    </div>
                </div>
            </div>

            @if($products->count() > 0)
                <div class="row tf-grid-layout lg-col-4 md-col-3 sm-col-2">
                    @foreach($products as $product)
                        @php
                            $productName = localizedField($product, 'name');
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card-product wow fadeInUp">
                                <div class="card-product_wrapper">
                                    <a href="{{ url('/product/' . $product->slug) }}" class="product-img">
                                        @if($product->images->first())
                                            <img class="img-product" loading="lazy" width="330" height="440"
                                                 src="{{ asset('storage/' . $product->images->first()->path) }}" alt="{{ $productName }}">
                                        @else
                                            <img class="img-product" loading="lazy" width="330" height="440"
                                                 src="{{ asset('assets/images/product/placeholder.jpg') }}" alt="{{ $productName }}">
                                        @endif
                                    </a>
                                    <ul class="product-action_list">
                                        <li class="wishlist">
                                            <a href="#;" class="hover-tooltip tooltip-left box-icon">
                                                <span class="icon icon-heart"></span>
                                                <span class="tooltip">В избранное</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#quickView" data-bs-toggle="offcanvas"
                                               class="hover-tooltip tooltip-left box-icon">
                                                <span class="icon icon-Eye"></span>
                                                <span class="tooltip">Быстрый просмотр</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-product_info">
                                    <a href="{{ url('/product/' . $product->slug) }}"
                                       class="name-product lh-24 fw-medium link-underline-text">
                                        {{ $productName }}
                                    </a>
                                    <div class="price-wrap">
                                        @if($product->variants->isNotEmpty())
                                            @php
                                                $minPrice = $product->variants->min('price_usd');
                                                $minFinalPrice = $product->variants->min('final_price_usd');
                                            @endphp
                                            @if($minFinalPrice < $minPrice)
                                                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minFinalPrice) }}</span>
                                                <span class="price-old text-caption-01 cl-text-3">{{ formatPriceByn($minPrice) }}</span>
                                            @else
                                                <span class="price-new text-primary fw-semibold">{{ formatPriceByn($minPrice) }}</span>
                                            @endif
                                        @else
                                            <span class="price-new text-primary fw-semibold">Цена по запросу</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="tf-page-pagination">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-body-1 cl-text-2">В данный момент нет доступных товаров этого бренда.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <!-- /Products Grid -->

@endsection
