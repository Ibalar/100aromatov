<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
        'slug',
        'name_ru',
        'name_by',
        'description_ru',
        'description_by',
        'country',
        'gender',
        'concentration',
        'is_active',
        'is_featured',
        'views',
        'seo_title_ru',
        'seo_title_by',
        'seo_description_ru',
        'seo_description_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_attribute_value'
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithActiveVariants($query)
    {
        return $query->with(['variants' => fn($q) => $q->where('is_active', true)]);
    }

    /* ================= ACCESSORS ================= */

    public function getMinPriceUsdAttribute()
    {
        return $this->variants()->min('price_usd');
    }
}
