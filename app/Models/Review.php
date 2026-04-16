<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'product_id',
        'rating',
        'text',
        'image',
        'admin_reply',
        'is_approved',
        'created_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->full_name;
        }

        if ($this->user) {
            return $this->user->name;
        }

        return 'Пользователь';
    }

    public function getTargetLabelAttribute(): string
    {
        return $this->product
            ? (string) localizedField($this->product, 'name')
            : 'Магазин';
    }
}
