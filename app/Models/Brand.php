<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description_ru',
        'description_by',
        'country',
        'logo',
        'seo_title_ru',
        'seo_title_by',
        'seo_description_ru',
        'seo_description_by',
        'h1_title_ru',
        'h1_title_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('brands.is_active', true);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('menu_brands');
            Cache::forget('brands_with_visible_product_counts');
            Cache::forget('brand_options');
            Cache::forget('home_brands');
        });

        static::deleted(function () {
            Cache::forget('menu_brands');
            Cache::forget('brands_with_visible_product_counts');
            Cache::forget('brand_options');
            Cache::forget('home_brands');
        });
    }
}
