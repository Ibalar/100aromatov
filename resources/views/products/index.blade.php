@extends('layouts.app')

@section('title', __('Каталог') . ' - ' . config('app.name'))
@section('meta_description', __('Полный каталог парфюмерии'))

@push('schema_org')
    <x-schema-org
        type="catalog"
        :title="__('Каталог') . ' - ' . config('app.name')"
        :description="__('Полный каталог парфюмерии')"
    />
@endpush

@section('content')

    <x-breadcrumbs
        :title="__('Каталог')"
        :items="[
            ['title' => __('Каталог')]
        ]"
    />

    <div class="flat-spacing-3">
        <div class="container">
            <div class="row">
                <!-- Sidebar with filters -->
                <aside class="col-lg-3 sidebar">
                    <div class="filter-sidebar">
                        <h5 class="filter-title">{{ __('Фильтры') }}</h5>

                        <form method="GET" action="{{ route('products.index') }}" class="filter-form">
                            @include('components.price-filter', ['minPrice' => $minPrice, 'maxPrice' => $maxPrice])

                            <!-- Brand Filter -->
                            <div class="filter-group">
                                <div class="filter-header" onclick="this.parentElement.classList.toggle('expanded')">
                                    <h6 class="title-filter">{{ __('Бренд') }}</h6>
                                    <span class="toggle-icon">
                                        <i class="icon icon-CaretDown"></i>
                                    </span>
                                </div>
                                <div class="filter-content">
                                    <select name="brand" class="form-select">
                                        <option value="">{{ __('Все бренды') }}</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ $brandFilter == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @include('components.attribute-filter', ['attributes' => $filterableAttributes, 'selectedAttributes' => $attributeFilters])

                            <div class="filter-actions">
                                <button type="submit" class="tf-btn btn-fill w-100">
                                    <span class="btn-text">{{ __('Применить') }}</span>
                                </button>
                                <a href="{{ route('products.index') }}" class="tf-btn btn-white w-100 mt-2">
                                    <span class="btn-text">{{ __('Сбросить') }}</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Products grid -->
                <div class="col-lg-9">
                    <div class="catalog-header">
                        <h1 class="catalog-title">{{ __('Каталог парфюмерии') }}</h1>
                        <div class="products-count">
                            {{ __('Найдено') }}: {{ $products->total() }} {{ trans_choice('товар|товара|товаров', $products->total()) }}
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="products-grid">
                            @foreach($products as $product)
                                @include('components.product-card', ['product' => $product])
                            @endforeach
                        </div>

                        <div class="pagination-wrapper">
                            {{ $products->links() }}
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

@push('styles')
<style>
    .sidebar {
        margin-bottom: 2rem;
    }
    .filter-sidebar {
        position: sticky;
        top: 2rem;
        background: #fff;
        padding: 1.5rem;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
    }
    .filter-title {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #eee;
    }
    .filter-form {
        margin-bottom: 0;
    }
    .filter-actions {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    .filter-group {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }
    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .filter-header .title-filter {
        margin: 0;
        font-size: 1rem;
    }
    .toggle-icon {
        transition: transform 0.3s;
    }
    .filter-group:not(.expanded) .toggle-icon {
        transform: rotate(-90deg);
    }
    .filter-group:not(.expanded) .filter-content {
        display: none;
    }
    .filter-content {
        padding-top: 0.75rem;
    }
    .catalog-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .catalog-title {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    .products-count {
        color: #888;
        font-size: 0.875rem;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }
    .empty-products {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 1rem;
    }
    .empty-products h4 {
        margin-bottom: 0.5rem;
        color: #333;
    }
    .empty-products p {
        color: #888;
    }
</style>
@endpush
