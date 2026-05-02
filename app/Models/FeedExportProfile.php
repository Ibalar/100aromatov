<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedExportProfile extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'shop_name',
        'company_name',
        'currency',
        'language',
        'include_category_slugs',
        'exclude_category_slugs',
        'include_brand_slugs',
        'exclude_brand_slugs',
        'only_in_stock',
        'include_inactive_products',
        'min_price_byn',
        'max_price_byn',
        'max_items',
    ];

    protected $casts = [
        'only_in_stock' => 'boolean',
        'include_inactive_products' => 'boolean',
        'min_price_byn' => 'decimal:2',
        'max_price_byn' => 'decimal:2',
        'max_items' => 'integer',
        'include_category_slugs' => 'array',
        'exclude_category_slugs' => 'array',
        'include_brand_slugs' => 'array',
        'exclude_brand_slugs' => 'array',
    ];
}
