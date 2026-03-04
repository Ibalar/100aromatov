<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_usd',
        'usage_limit',
        'usage_per_user',
        'active_from',
        'active_to',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_usd' => 'decimal:2',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('active_from')
                    ->orWhere('active_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('active_to')
                    ->orWhere('active_to', '>=', now());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Logic
    |--------------------------------------------------------------------------
    */

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percent') {
            return $amount * ($this->value / 100);
        }

        return min($this->value, $amount);
    }
}
