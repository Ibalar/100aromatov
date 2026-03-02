<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterPage extends Model
{
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
    ];

    protected $casts = [
        'filter_data' => 'array',
        'is_indexable' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
