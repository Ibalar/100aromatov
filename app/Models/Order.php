<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_usd',
        'total_byn',
        'promo_code',
        'discount_usd',
        'phone',
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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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
