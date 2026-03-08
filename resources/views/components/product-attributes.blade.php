@props(['attributeValues'])

@if($attributeValues->count() > 0)
    <div class="product-attributes">
        <h6 class="attributes-title">{{ __('Характеристики') }}</h6>
        <div class="attributes-list">
            @php
                $groupedByAttribute = $attributeValues->groupBy('attribute_id');
            @endphp
            @foreach($groupedByAttribute as $attributeId => $values)
                @php
                    $attribute = $values->first()->attribute;
                @endphp
                <div class="attribute-row">
                    <span class="attribute-name">{{ localizedField($attribute, 'name') }}:</span>
                    <span class="attribute-values">
                        @foreach($values as $value)
                            <span class="attribute-value">{{ localizedField($value, 'value') }}</span>
                            @if(!$loop->last)
                                <span class="value-separator">, </span>
                            @endif
                        @endforeach
                    </span>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('styles')
<style>
    .product-attributes {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .attributes-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .attributes-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .attribute-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #dee2e6;
    }
    .attribute-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .attribute-name {
        font-weight: 500;
        color: #666;
        flex-shrink: 0;
        margin-right: 1rem;
    }
    .attribute-values {
        text-align: right;
        color: #333;
    }
    .attribute-value {
        font-weight: 500;
    }
    .value-separator {
        color: #999;
    }
</style>
@endpush
