<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceLog extends Model
{
    protected $fillable = [
        'product_variant_id',
        'old_price_usd',
        'new_price_usd',
    ];

    protected $casts = [
        'old_price_usd' => 'decimal:2',
        'new_price_usd' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
