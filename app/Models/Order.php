<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use RuntimeException;

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
        'status' => OrderStatus::class,
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
            $volume = filled($item->volume_ml_snapshot) ? "{$item->volume_ml_snapshot} ml" : null;
            $variantMeta = array_filter([
                $item->sku_snapshot ? "SKU: {$item->sku_snapshot}" : null,
                $volume ? "Объем: {$volume}" : null,
            ]);
            $variantSummary = $variantMeta !== [] ? ' | '.implode(' | ', $variantMeta) : '';

            return "{$item->name_snapshot}{$variantSummary} | {$item->qty} x {$price} BYN = {$lineTotal} BYN";
        })->implode("\n");
    }

    /*
    |--------------------------------------------------------------------------
    | Status Transitions
    |--------------------------------------------------------------------------
    */

    public function transitionTo(OrderStatus $newStatus): void
    {
        /** @var OrderStatus $currentStatus */
        $currentStatus = $this->status;

        if (! $currentStatus instanceof OrderStatus) {
            Log::warning('Order: status is not an OrderStatus instance', [
                'order_id' => $this->id,
                'raw' => $this->getRawOriginal('status'),
            ]);

            $currentStatus = OrderStatus::tryFrom((string) $this->getRawOriginal('status'))
                ?? OrderStatus::New;
        }

        if (! $currentStatus->canTransitionTo($newStatus)) {
            throw new RuntimeException(sprintf(
                'Недопустимый переход статуса заказа #%d: %s → %s. Разрешённые переходы: %s',
                $this->id,
                $currentStatus->label(),
                $newStatus->label(),
                implode(', ', array_map(fn (OrderStatus $s) => $s->label(), $currentStatus->allowedTransitions()))
            ));
        }

        $oldStatus = $currentStatus;

        $this->status = $newStatus;
        $this->save();

        Log::info('Order: status changed', [
            'order_id' => $this->id,
            'from' => $oldStatus->value,
            'to' => $newStatus->value,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isNew(): bool
    {
        return $this->status === OrderStatus::New;
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isProcessing(): bool
    {
        return $this->status === OrderStatus::Processing;
    }

    public function isShipped(): bool
    {
        return $this->status === OrderStatus::Shipped;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function isCanceled(): bool
    {
        return $this->status === OrderStatus::Canceled;
    }

    /**
     * @deprecated Use isCompleted() instead. Kept for backward compatibility.
     */
    public function isConfirmed(): bool
    {
        return $this->isCompleted();
    }
}
