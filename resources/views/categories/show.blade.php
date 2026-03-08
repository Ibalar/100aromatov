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

    <div class="flat-spacing-3">
        <div class="container">
            <div class="row">
                <!-- Sidebar with filters -->
                <aside class="col-lg-3 sidebar">
                    <div class="filter-sidebar">
                        <h5 class="filter-title">{{ __('Фильтры') }}</h5>

                        <form method="GET" action="{{ route('category.show', $category->slug) }}" class="filter-form">
                            @include('components.price-filter', ['priceRange' => $priceRange, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice])

                            @include('components.attribute-filter', ['attributes' => $filterableAttributes, 'selectedAttributes' => $attributeFilters])

                            <div class="filter-actions">
                                <button type="submit" class="tf-btn btn-fill w-100">
                                    <span class="btn-text">{{ __('Применить') }}</span>
                                </button>
                                <a href="{{ route('category.show', $category->slug) }}" class="tf-btn btn-white w-100 mt-2">
                                    <span class="btn-text">{{ __('Сбросить') }}</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Products grid -->
                <div class="col-lg-9">
                    <div class="category-header">
                        <h1 class="category-title">{{ localizedField($category, 'name') }}</h1>
                        @if($category->description_ru || $category->description_by)
                            <p class="category-description">{{ localizedField($category, 'description') }}</p>
                        @endif
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
    .category-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .category-title {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    .category-description {
        color: #666;
        margin-bottom: 1rem;
        line-height: 1.6;
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
