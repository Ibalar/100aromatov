@extends('layouts.app')

@section('title', __('Категории') . ' - ' . config('app.name'))
@section('meta_description', __('Каталог парфюмерии по категориям'))

@push('schema_org')
    <x-schema-org
        type="categories_list"
        :title="__('Категории') . ' - ' . config('app.name')"
        :description="__('Каталог парфюмерии по категориям')"
    />
@endpush

@section('content')

    <x-breadcrumbs
        :title="__('Категории')"
        :items="[
            ['title' => __('Категории')]
        ]"
    />

    <div class="flat-spacing-3">
        <div class="container">
            <h1 class="page-title">{{ __('Категории') }}</h1>

            @if(count($tree) > 0)
                <div class="categories-grid">
                    @foreach($tree as $category)
                        @include('categories._category_item', ['category' => $category])
                    @endforeach
                </div>
            @else
                <div class="empty-categories">
                    <p>{{ __('Категории временно недоступны') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('styles')
<style>
    .page-title {
        font-size: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }
    .category-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow 0.3s;
        background: #fff;
    }
    .category-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .category-link {
        display: block;
        padding: 1.5rem;
        text-decoration: none;
        color: inherit;
    }
    .category-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }
    .category-count {
        font-size: 0.875rem;
        color: #666;
        margin-bottom: 0.75rem;
    }
    .category-description {
        font-size: 0.875rem;
        color: #888;
        line-height: 1.5;
    }
    .subcategories {
        padding: 0 1.5rem 1.5rem;
    }
    .subcategory-link {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        margin: 0.25rem;
        background: #f0f0f0;
        border-radius: 20px;
        font-size: 0.75rem;
        text-decoration: none;
        color: #555;
        transition: background 0.2s;
    }
    .subcategory-link:hover {
        background: #e0e0e0;
    }
    .empty-categories {
        text-align: center;
        padding: 3rem;
        color: #666;
    }
</style>
@endpush
