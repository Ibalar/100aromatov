<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterPage extends Model
{
    protected $attributes = [
        'filter_data' => '[]',
        'is_indexable' => true,
        'show_in_category_tiles' => true,
    ];

    protected $fillable = [
        'category_id',
        'slug',
        'filter_data',
        'h1_ru',
        'h1_by',
        'seo_title_ru',
        'seo_title_by',
        'seo_description_ru',
        'seo_description_by',
        'seo_text_ru',
        'seo_text_by',
        'is_indexable',
        'show_in_category_tiles',
    ];

    protected $casts = [
        'filter_data' => 'array',
        'is_indexable' => 'boolean',
        'show_in_category_tiles' => 'boolean',
    ];

    public function setFilterDataAttribute($value): void
    {
        if (\is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        $data = \is_array($value) ? $value : [];

        $brands = array_values(array_filter(array_map(
            static fn ($id) => (int) $id,
            (array) ($data['brand'] ?? [])
        )));

        $attributes = array_values(array_filter(array_map(static function ($row) {
            if (! \is_array($row)) {
                return null;
            }

            $attributeId = (int) ($row['attribute_id'] ?? 0);
            $valueIds = array_values(array_filter(array_map(
                static fn ($id) => (int) $id,
                (array) ($row['value_ids'] ?? [])
            )));

            if ($attributeId <= 0 || $valueIds === []) {
                return null;
            }

            return [
                'attribute_id' => $attributeId,
                'value_ids' => $valueIds,
            ];
        }, (array) ($data['attributes'] ?? []))));

        $normalized = [
            'brand' => $brands,
            'attributes' => $attributes,
            'min_price' => isset($data['min_price']) && $data['min_price'] !== '' ? (float) $data['min_price'] : null,
            'max_price' => isset($data['max_price']) && $data['max_price'] !== '' ? (float) $data['max_price'] : null,
        ];

        $this->attributes['filter_data'] = json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
