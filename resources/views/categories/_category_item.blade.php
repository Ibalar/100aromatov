@php
    $hasChildren = !empty($category->children) && count($category->children) > 0;
@endphp

<div class="category-card">
    <a href="{{ route('category.show', $category->slug) }}" class="category-link">
        <h3 class="category-name">{{ localizedField($category, 'name') }}</h3>
        @if($category->products_count > 0)
            <div class="category-count">
                {{ $category->products_count }} {{ trans_choice('товар|товара|товаров', $category->products_count) }}
            </div>
        @endif
        @if($category->description_ru || $category->description_by)
            <p class="category-description">
                {{ Str::limit(localizedField($category, 'description'), 100) }}
            </p>
        @endif
    </a>

    @if($hasChildren)
        <div class="subcategories">
            @foreach($category->children as $child)
                <a href="{{ route('category.show', $child->slug) }}" class="subcategory-link">
                    {{ localizedField($child, 'name') }}
                </a>
            @endforeach
        </div>
    @endif
</div>
