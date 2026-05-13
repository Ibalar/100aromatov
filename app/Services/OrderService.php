<?php

namespace App\Services;

use App\Models\{
    Order,
    OrderItem,
    ProductVariant,
    PromoCode,
    PromoCodeUsage,
    Setting
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $customerId = auth('customer')->id();
            $webUserId = auth('web')->id();

            $pricing = $this->calculatePricing(
                $data['items'],
                (string) ($data['promo_code'] ?? ''),
                $customerId,
                $data['phone'] ?? null,
                true
            );

            $settings = Setting::getSettings();
            $totalByn = $settings->convertUsdToByn($pricing['total_usd']);

            $order = Order::create([
                'user_id' => $webUserId,
                'customer_id' => $customerId,
                'status' => 'new',
                'total_usd' => $pricing['total_usd'],
                'total_byn' => $totalByn,
                'promo_code' => $pricing['applied_promo_code'],
                'discount_usd' => $pricing['discount_usd'],
                'phone' => $data['phone'],
                'call_preference' => $data['call_preference'] ?? 'call_me',
                'email' => $data['email'] ?? '',
            ]);

            if ($pricing['promo_model']) {
                /** @var PromoCode $promo */
                $promo = $pricing['promo_model'];
                $promo->increment('used_count');

                PromoCodeUsage::create([
                    'promo_code_id' => $promo->id,
                    'user_id' => $webUserId,
                    'customer_id' => $customerId,
                    'phone' => $data['phone'] ?? null,
                    'order_id' => $order->id,
                ]);
            }

            foreach ($pricing['items'] as $item) {
                $priceByn = $settings->convertUsdToByn($item['price_usd']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'name_snapshot' => $item['variant']->product->name_ru,
                    'sku_snapshot' => $item['variant']->sku,
                    'volume_ml_snapshot' => $item['variant']->volume_ml,
                    'qty' => $item['qty'],
                    'price_byn_snapshot' => $priceByn,
                ]);
            }

            $sent = app(TelegramService::class)->send(
                $this->buildTelegramMessage($order, $pricing['items'])
            );

            if (! $sent) {
                Log::warning('Order created but Telegram notification failed', [
                    'order_id' => $order->id,
                ]);
            }

            return $order;
        });
    }

    public function calculatePreview(array $items, ?string $promoCode, ?int $customerId, ?string $phone): array
    {
        return $this->calculatePricing($items, (string) $promoCode, $customerId, $phone, false);
    }

    private function calculatePricing(array $items, string $promoCode, ?int $customerId, ?string $phone, bool $throwOnInvalidPromo): array
    {
        $itemsData = [];
        $regularTotalUsd = 0.0;
        $saleTotalUsd = 0.0;

        foreach ($items as $item) {
            $variant = ProductVariant::findOrFail((int) $item['variant_id']);
            $qty = (int) $item['qty'];

            $regularUsd = (float) $variant->price_usd;
            $saleUsd = (float) $variant->final_price_usd;

            $itemsData[] = [
                'variant' => $variant,
                'qty' => $qty,
                'regular_usd' => $regularUsd,
                'sale_usd' => $saleUsd,
                'price_usd' => $saleUsd,
            ];

            $regularTotalUsd += $regularUsd * $qty;
            $saleTotalUsd += $saleUsd * $qty;
        }

        $promo = null;
        $promoError = null;
        $discountUsd = 0.0;
        $totalUsd = $saleTotalUsd;

        $promoCode = mb_strtoupper(trim($promoCode));
        if ($promoCode !== '') {
            $promoQuery = PromoCode::query()
                ->whereRaw('UPPER(code) = ?', [$promoCode]);

            if ($throwOnInvalidPromo) {
                $promoQuery->lockForUpdate();
            }

            $promo = $promoQuery->first();

            if (! $promo) {
                $promoError = 'Промокод не найден.';
            } else {
                $promoError = $promo->getValidationError($customerId, $phone, $regularTotalUsd);
            }

            if ($promoError !== null) {
                if ($throwOnInvalidPromo) {
                    throw ValidationException::withMessages([
                        'promo_code' => $promoError,
                    ]);
                }

                return [
                    'items' => $itemsData,
                    'total_usd' => $saleTotalUsd,
                    'discount_usd' => 0.0,
                    'regular_total_usd' => $regularTotalUsd,
                    'total_discount_usd' => max(0.0, $regularTotalUsd - $saleTotalUsd),
                    'applied_promo_code' => null,
                    'promo_model' => null,
                    'promo_error' => $promoError,
                ];
            }

            if ($regularTotalUsd > 0) {
                $promoItemsTotalUsd = 0.0;
                $promoDiscountTotalUsd = $promo->calculateDiscount($regularTotalUsd);
                $priceFactor = $promo->type === 'percent'
                    ? max(0.0, 1 - ((float) $promo->value / 100))
                    : 1.0;

                foreach ($itemsData as &$itemData) {
                    $regularLineUsd = $itemData['regular_usd'] * $itemData['qty'];

                    if ($promo->type === 'percent') {
                        $promoLineUsd = $regularLineUsd * $priceFactor;
                    } elseif ($promo->type === 'fixed') {
                        $lineShare = $promoDiscountTotalUsd * ($regularLineUsd / $regularTotalUsd);
                        $promoLineUsd = max(0.0, $regularLineUsd - $lineShare);
                    } else {
                        throw new RuntimeException('Unsupported promo type');
                    }

                    $promoUnitUsd = $itemData['qty'] > 0 ? ($promoLineUsd / $itemData['qty']) : $itemData['regular_usd'];
                    $effectiveUnitUsd = min($itemData['sale_usd'], $promoUnitUsd);
                    $itemData['price_usd'] = $effectiveUnitUsd;
                    $promoItemsTotalUsd += $effectiveUnitUsd * $itemData['qty'];
                }
                unset($itemData);

                if ($promoItemsTotalUsd < $saleTotalUsd) {
                    $discountUsd = $saleTotalUsd - $promoItemsTotalUsd;
                    $totalUsd = $promoItemsTotalUsd;
                } else {
                    $promo = null;
                    foreach ($itemsData as &$itemData) {
                        $itemData['price_usd'] = $itemData['sale_usd'];
                    }
                    unset($itemData);
                }
            }
        }

        return [
            'items' => $itemsData,
            'total_usd' => $totalUsd,
            'discount_usd' => $discountUsd,
            'regular_total_usd' => $regularTotalUsd,
            'total_discount_usd' => max(0.0, $regularTotalUsd - $totalUsd),
            'applied_promo_code' => $promo?->code,
            'promo_model' => $promo,
            'promo_error' => null,
        ];
    }

    protected function buildTelegramMessage(Order $order, array $itemsData = []): string
    {
        if (! $order->relationLoaded('items')) {
            $order->load('items');
        }

        $itemMetaBySku = [];
        foreach ($itemsData as $itemData) {
            $variant = $itemData['variant'] ?? null;
            if (! $variant instanceof ProductVariant) {
                continue;
            }

            $variant->loadMissing(['product.images']);
            $skuKey = (string) ($variant->sku ?? '');

            $itemMetaBySku[$skuKey] = [
                'product_url' => $this->getProductUrl($variant),
            ];
        }

        $totalByn = number_format((float) $order->total_byn, 2, ',', ' ');
        $message = "<b>Новый заказ #{$order->id}</b>\n";
        $message .= "Телефон: " . $this->escape($order->phone) . "\n";

        $callPreference = $order->call_preference === 'no_call'
            ? 'Перезванивать не нужно'
            : 'Перезвоните';
        $message .= "Перезвон: " . $this->escape($callPreference) . "\n";

        if ($order->email) {
            $message .= "Email: " . $this->escape($order->email) . "\n";
        }

        if ($order->promo_code) {
            $message .= "Промокод: " . $this->escape($order->promo_code) . "\n";
        }

        $message .= "Сумма: {$totalByn} BYN\n\n";
        $message .= "<b>Позиции:</b>\n";

        foreach ($order->items as $item) {
            $name = $this->escape($item->name_snapshot);
            $sku = $this->escape($item->sku_snapshot);
            $volume = filled($item->volume_ml_snapshot)
                ? $this->escape((string) $item->volume_ml_snapshot) . ' ml'
                : null;
            $price = number_format((float) $item->price_byn_snapshot, 2, ',', ' ');
            $line = number_format((float) $item->price_byn_snapshot * $item->qty, 2, ',', ' ');
            $variantMeta = [];

            if ($sku !== '') {
                $variantMeta[] = "SKU: {$sku}";
            }

            if ($volume !== null) {
                $variantMeta[] = "Объем: {$volume}";
            }

            $message .= "• {$name}";
            if ($variantMeta !== []) {
                $message .= ' (' . implode(', ', $variantMeta) . ')';
            }
            $message .= "\n";
            $message .= "  {$item->qty} x {$price} BYN = {$line} BYN\n";

            $itemMeta = $itemMetaBySku[(string) $item->sku_snapshot] ?? null;
            if (is_array($itemMeta) && filled($itemMeta['product_url'])) {
                $message .= "  Ссылка на товар: " . $this->escape($itemMeta['product_url']) . "\n";
            }
        }

        return $message;
    }

    private function getProductUrl(ProductVariant $variant): ?string
    {
        $slug = $variant->product?->slug;

        if (! filled($slug)) {
            return null;
        }

        return route('product.show', ['slug' => $slug], true);
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
