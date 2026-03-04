<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'name_snapshot',
        'sku_snapshot',
        'qty',
        'price_byn_snapshot',
    ];

    protected $casts = [
        'price_byn_snapshot' => 'decimal:2',
        'qty' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute(): float
    {
        return $this->qty * $this->price_byn_snapshot;
    }
}
