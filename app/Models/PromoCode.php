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
        return $query->where('promo_codes.is_active', true)
            ->where(function ($q) {
                $q->whereNull('promo_codes.active_from')
                    ->orWhere('promo_codes.active_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('promo_codes.active_to')
                    ->orWhere('promo_codes.active_to', '>=', now());
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

    public function usages()
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function canBeUsedBy(?int $customerId, float $orderAmount): bool
    {
        return $this->getValidationError($customerId, $orderAmount) === null;
    }

    public function getValidationError(?int $customerId, float $orderAmount): ?string
    {
        if (!$this->is_active) {
            return 'Промокод отключен.';
        }

        if ($this->active_from && $this->active_from->isFuture()) {
            return 'Промокод еще не активен.';
        }

        if ($this->active_to && $this->active_to->isPast()) {
            return 'Срок действия промокода истек.';
        }

        if ($this->min_order_usd && $orderAmount < $this->min_order_usd) {
            return 'Сумма заказа меньше минимальной для этого промокода.';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Лимит использований этого промокода исчерпан.';
        }

        if ($this->usage_per_user && $customerId) {
            $userCount = $this->usages()
                ->where('customer_id', $customerId)
                ->count();

            if ($userCount >= $this->usage_per_user) {
                return 'Вы уже использовали этот промокод максимальное количество раз.';
            }
        }

        return null;
    }
}
