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
        'min_price',
        'max_price',
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
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
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

    /* ================= HELPERS ================= */

    /**
     * Update min and max prices based on active variants.
     */
    public function updatePriceRange(): void
    {
        $prices = $this->variants()
            ->where('is_active', true)
            ->pluck('price_usd');

        $this->update([
            'min_price' => $prices->min(),
            'max_price' => $prices->max(),
        ]);
    }
}
