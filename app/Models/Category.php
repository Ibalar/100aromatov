<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'depth',
        'slug',
        'name_ru',
        'name_by',
        'description_ru',
        'description_by',
        'image',
        'seo_title_ru',
        'seo_title_by',
        'seo_description_ru',
        'seo_description_by',
        'seo_text_ru',
        'seo_text_by',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /* ================= RELATIONS ================= */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function filterPages(): HasMany
    {
        return $this->hasMany(FilterPage::class);
    }

    /* ================= SCOPES ================= */

    public function scopeActive($query)
    {
        return $query->where('categories.is_active', true);
    }

    protected static function booted()
    {
        static::addGlobalScope('sorted', function ($query) {
            $query->orderBy('sort_order')
                ->orderBy('id');
        });

        $flushCategoryCache = static function (): void {
            Cache::forget('category_tree_active');
        };

        static::saved($flushCategoryCache);
        static::deleted($flushCategoryCache);
    }

    /* ================= ACCESSORS ================= */

    public function getProductsCountAttribute()
    {
        $categoryIds = $this->getDescendantIds();
        return Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->count();
    }

    /* ================= HELPERS ================= */

    public function getDescendantIds(): array
    {
        return $this->gatherDescendantIds($this->id);
    }


    private function gatherDescendantIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = self::where('parent_id', $categoryId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->gatherDescendantIds($childId));
        }

        return $ids;
    }

    public function getAllProductIds(): array
    {
        $categoryIds = $this->getDescendantIds();
        return Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();
    }
}
