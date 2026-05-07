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
        'old_url',
        'name_ru',
        'name_by',
        'description_ru',
        'description_by',
        'country',
        'country_by',
        'gender',
        'gender_by',
        'concentration',
        'concentration_by',
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

    public function setNameRuAttribute(?string $value): void
    {
        $this->attributes['name_ru'] = $this->decodeHtmlEntities($value);
    }

    public function setNameByAttribute(?string $value): void
    {
        $this->attributes['name_by'] = $this->decodeHtmlEntities($value);
    }

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
        return $this->hasMany(ProductVariant::class)
            ->orderByRaw('CASE WHEN price_usd = 0 THEN 1 ELSE 0 END')
            ->orderBy('price_usd')
            ->orderBy('id');
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
        )->orderBy('value_ru');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('products.is_active', true);
    }

    public function scopeWithActiveVariants($query)
    {
        return $query->with(['variants' => fn($q) => $q->where('is_active', true)]);
    }

    /* ================= ACCESSORS ================= */

    public function getMinPriceUsdAttribute()
    {
        $pricedVariants = $this->variants()->where('price_usd', '>', 0);

        return $pricedVariants->exists()
            ? $pricedVariants->min('price_usd')
            : $this->variants()->min('price_usd');
    }

    /* ================= HELPERS ================= */

    /**
     * Update min and max prices based on active variants.
     */
    public function updatePriceRange(): void
    {
        $variants = $this->variants()
            ->where('is_active', true)
            ->get(['price_usd']);

        $positivePrices = $variants
            ->pluck('price_usd')
            ->filter(static fn ($price) => (float) $price > 0)
            ->values();

        $prices = $positivePrices->isNotEmpty()
            ? $positivePrices
            : $variants->pluck('price_usd');

        $this->update([
            'min_price' => $prices->isEmpty() ? null : $prices->min(),
            'max_price' => $prices->isEmpty() ? null : $prices->max(),
        ]);
    }

    private function decodeHtmlEntities(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
