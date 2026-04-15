<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'name_ru',
        'name_by',
        'description_ru',
        'description_by',
        'seo_title_ru',
        'seo_title_by',
        'seo_description_ru',
        'seo_description_by',
        'sort_order',
        'is_active',
        'show_in_menu',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    protected static function booted(): void
    {
        $forgetMenuCache = static function (): void {
            Cache::forget('menu_pages');
        };

        static::saved($forgetMenuCache);
        static::deleted($forgetMenuCache);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeMenu(Builder $query): Builder
    {
        return $query->active()->where('show_in_menu', true);
    }
}
