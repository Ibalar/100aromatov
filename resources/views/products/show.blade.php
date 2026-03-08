@extends('layouts.app')

@section('title', localizedField($product, 'name') . ' - ' . config('app.name'))
@section('meta_description', localizedField($product, 'seo_description') ?: Str::limit(strip_tags(localizedField($product, 'description')), 160))

@push('schema_org')
    <x-schema-org
        type="product"
        :title="localizedField($product, 'name') . ' - ' . config('app.name')"
        :description="localizedField($product, 'seo_description') ?: localizedField($product, 'description')"
        :product="$product"
    />
@endpush

@section('content')

    <x-breadcrumbs
        :title="localizedField($product, 'name')"
        :items="[
            ['title' => __('Каталог'), 'url' => route('products.index')],
            $product->category ? ['title' => localizedField($product->category, 'name'), 'url' => route('category.show', $product->category->slug)] : null,
            ['title' => localizedField($product, 'name')]
        ]"
    />

    <div class="flat-spacing-3">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images">
                        @if($product->images->count() > 0)
                            <div class="main-image">
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}"
                                     alt="{{ localizedField($product->images->first(), 'alt') ?: localizedField($product, 'name') }}"
                                     class="img-fluid"
                                     id="main-product-image">
                            </div>
                            @if($product->images->count() > 1)
                                <div class="thumbnail-gallery">
                                    @foreach($product->images as $image)
                                        <div class="thumbnail-item {{ $loop->first ? 'active' : '' }}"
                                             onclick="document.getElementById('main-product-image').src = '{{ asset('storage/' . $image->path) }}';
                                                      document.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                                                      this.classList.add('active');">
                                            <img src="{{ asset('storage/' . $image->path) }}"
                                                 alt="{{ localizedField($image, 'alt') ?: localizedField($product, 'name') }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="no-image">
                                <span>{{ __('Изображение отсутствует') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-title">{{ localizedField($product, 'name') }}</h1>

                        @if($product->brand)
                            <div class="product-brand">
                                <a href="{{ route('brand.show', $product->brand->slug) }}">
                                    {{ $product->brand->name }}
                                </a>
                            </div>
                        @endif

                        <div class="product-meta">
                            @if($product->country)
                                <span class="meta-item">
                                    <strong>{{ __('Страна') }}:</strong> {{ $product->country }}
                                </span>
                            @endif
                            @if($product->gender)
                                <span class="meta-item">
                                    <strong>{{ __('Пол') }}:</strong>
                                    @if($product->gender == 'male')
                                        {{ __('Мужской') }}
                                    @elseif($product->gender == 'female')
                                        {{ __('Женский') }}
                                    @else
                                        {{ __('Унисекс') }}
                                    @endif
                                </span>
                            @endif
                            @if($product->concentration)
                                <span class="meta-item">
                                    <strong>{{ __('Концентрация') }}:</strong> {{ $product->concentration }}
                                </span>
                            @endif
                        </div>

                        <!-- Product Variants -->
                        @include('components.product-variants', ['variants' => $product->variants, 'product' => $product])

                        <!-- Product Description -->
                        @if($product->description_ru || $product->description_by)
                            <div class="product-description">
                                <h6>{{ __('Описание') }}</h6>
                                <div class="description-content">
                                    {!! localizedField($product, 'description') !!}
                                </div>
                            </div>
                        @endif

                        <!-- Product Attributes -->
                        @include('components.product-attributes', ['attributeValues' => $product->attributeValues])

                        <!-- Product Views -->
                        <div class="product-views">
                            <i class="icon icon-Eye"></i>
                            <span>{{ $product->views }} {{ trans_choice('просмотр|просмотра|просмотров', $product->views) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            @if($product->reviews->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="reviews-section">
                            <h3 class="reviews-title">{{ __('Отзывы') }} ({{ $product->reviews->count() }})</h3>
                            <div class="reviews-list">
                                @foreach($product->reviews as $review)
                                    <div class="review-item">
                                        <div class="review-header">
                                            <span class="review-author">{{ $review->user?->name ?? __('Пользователь') }}</span>
                                            <span class="review-date">{{ $review->created_at->format('d.m.Y') }}</span>
                                        </div>
                                        <div class="review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="icon icon-Star{{ $i <= $review->rating ? 'Fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="review-text">
                                            {{ $review->text }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('styles')
<style>
    .product-images {
        margin-bottom: 2rem;
    }
    .main-image {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .main-image img {
        width: 100%;
        height: auto;
        object-fit: contain;
    }
    .thumbnail-gallery {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .thumbnail-item {
        width: 80px;
        height: 80px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .thumbnail-item.active,
    .thumbnail-item:hover {
        border-color: #007bff;
    }
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        background: #f8f9fa;
        border: 1px dashed #ccc;
        border-radius: 12px;
        color: #888;
    }
    .product-info {
        padding-left: 1rem;
    }
    .product-title {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    .product-brand {
        margin-bottom: 1rem;
    }
    .product-brand a {
        color: #007bff;
        font-size: 1.125rem;
        text-decoration: none;
    }
    .product-brand a:hover {
        text-decoration: underline;
    }
    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    .meta-item {
        font-size: 0.875rem;
        color: #666;
    }
    .meta-item strong {
        color: #333;
    }
    .product-description {
        margin-bottom: 2rem;
    }
    .product-description h6 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .description-content {
        line-height: 1.7;
        color: #555;
    }
    .product-views {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #888;
        font-size: 0.875rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    .reviews-section {
        padding-top: 3rem;
        border-top: 1px solid #eee;
    }
    .reviews-title {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .review-item {
        padding: 1.5rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .review-author {
        font-weight: 600;
    }
    .review-date {
        color: #888;
        font-size: 0.875rem;
    }
    .review-rating {
        color: #ffc107;
        margin-bottom: 0.75rem;
    }
    .review-text {
        color: #555;
        line-height: 1.6;
    }
</style>
@endpush
