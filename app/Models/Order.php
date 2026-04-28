<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'status',
        'total_usd',
        'total_byn',
        'promo_code',
        'discount_usd',
        'phone',
        'call_preference',
        'email',
    ];

    protected $casts = [
        'total_usd' => 'decimal:2',
        'total_byn' => 'decimal:2',
        'discount_usd' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getItemsSummaryAttribute(): string
    {
        if (! $this->relationLoaded('items')) {
            $this->load('items');
        }

        if ($this->items->isEmpty()) {
            return '-';
        }

        return $this->items->map(function (OrderItem $item): string {
            $lineTotal = number_format((float) $item->price_byn_snapshot * $item->qty, 2, ',', ' ');
            $price = number_format((float) $item->price_byn_snapshot, 2, ',', ' ');

            return "{$item->name_snapshot} | SKU: {$item->sku_snapshot} | {$item->qty} x {$price} BYN = {$lineTotal} BYN";
        })->implode("\n");
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
