<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'volume_ml',
        'price_usd',
        'sale_price_usd',
        'is_tester',
        'is_raspiv',
        'is_unboxed',
        'is_gift_wrapped',
        'is_exclusive',
        'is_active',
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'sale_price_usd' => 'decimal:2',
        'is_tester' => 'boolean',
        'is_raspiv' => 'boolean',
        'is_unboxed' => 'boolean',
        'is_gift_wrapped' => 'boolean',
        'is_exclusive' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFinalPriceUsdAttribute()
    {
        return $this->sale_price_usd ?? $this->price_usd;
    }
}
