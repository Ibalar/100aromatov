@props(['attributes', 'selectedAttributes' => []])

<div class="attribute-filters">
    @foreach($attributes as $attribute)
        @php
            $attributeId = $attribute->id;
            $isExpanded = !empty($selectedAttributes[$attributeId]);
        @endphp
        <div class="filter-group {{ $isExpanded ? 'expanded' : '' }}">
            <div class="filter-header" onclick="this.parentElement.classList.toggle('expanded')">
                <h6 class="title-filter">{{ localizedField($attribute, 'name') }}</h6>
                <span class="toggle-icon">
                    <i class="icon icon-CaretDown"></i>
                </span>
            </div>
            <div class="filter-content">
                @foreach($attribute->values as $value)
                    @php
                        $isChecked = isset($selectedAttributes[$attributeId]) &&
                                     in_array($value->id, $selectedAttributes[$attributeId]);
                    @endphp
                    <label class="filter-checkbox">
                        <input type="checkbox"
                               name="attributes[{{ $attributeId }}][]"
                               value="{{ $value->id }}"
                               {{ $isChecked ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        <span class="label-text">{{ localizedField($value, 'value') }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

@push('styles')
<style>
    .attribute-filters {
        margin-bottom: 1.5rem;
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
    .filter-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }
    .filter-checkbox input[type="checkbox"] {
        display: none;
    }
    .filter-checkbox .checkmark {
        width: 18px;
        height: 18px;
        border: 2px solid #ccc;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .filter-checkbox input[type="checkbox"]:checked + .checkmark {
        background-color: #007bff;
        border-color: #007bff;
    }
    .filter-checkbox input[type="checkbox"]:checked + .checkmark::after {
        content: '\2714';
        color: white;
        font-size: 12px;
    }
    .filter-checkbox .label-text {
        font-size: 0.875rem;
    }
</style>
@endpush
