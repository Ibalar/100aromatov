@props(['attributes', 'selectedAttributes' => []])

@foreach($attributes as $attribute)
    @php
        $attributeId = $attribute->id;
        $isExpanded = !empty($selectedAttributes[$attributeId]);
    @endphp
    <div class="widget-facet">
        <div class="facet-title" data-bs-target="#attr_{{ $attribute->id }}" role="button"
            data-bs-toggle="collapse" aria-expanded="true" aria-controls="attr_{{ $attribute->id }}">
            <h6>{{ localizedField($attribute, 'name') }}</h6>
            <span class="icon icon-CaretDown"></span>
        </div>
        <div id="attr_{{ $attribute->id }}" class="collapse show">
            <ul class="collapse-body filter-group-check">
                @foreach($attribute->values as $value)
                    @php
                        $isChecked = isset($selectedAttributes[$attributeId]) &&
                                     in_array($value->id, $selectedAttributes[$attributeId]);
                    @endphp
                    <li class="list-item">
                        <input type="checkbox" class="tf-check" name="attributes[{{ $attributeId }}][]" id="attr_{{ $attributeId }}_{{ $value->id }}" value="{{ $value->id }}" {{ $isChecked ? 'checked' : '' }}>
                        <label for="attr_{{ $attributeId }}_{{ $value->id }}" class="label">
                            <span class="cate-text">{{ localizedField($value, 'value') }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="br-line"></div>
@endforeach
