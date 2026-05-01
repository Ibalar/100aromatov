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

class OrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $settings = Setting::getSettings();

            $totalUsd = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {

                $variant = ProductVariant::findOrFail($item['variant_id']);

                $priceUsd = (float) $variant->final_price_usd;

                $totalUsd += $priceUsd * $item['qty'];

                $itemsData[] = [
                    'variant' => $variant,
                    'price_usd' => $priceUsd,
                    'qty' => $item['qty'],
                ];
            }

            $discountUsd = 0;
            $promo = null;

            if (!empty($data['promo_code'])) {

                $promo = PromoCode::where('code', $data['promo_code'])
                    ->lockForUpdate()
                    ->first();

                if ($promo && $promo->canBeUsedBy(auth()->id(), $totalUsd)) {
                    $discountUsd = $promo->calculateDiscount($totalUsd);
                } else {
                    $promo = null;
                }
            }

            $totalUsdFinal = max(0, $totalUsd - $discountUsd);
            $totalByn = $settings->convertUsdToByn($totalUsdFinal);

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_id' => auth('customer')->id(),
                'status' => 'new',
                'total_usd' => $totalUsdFinal,
                'total_byn' => $totalByn,
                'promo_code' => $promo?->code,
                'discount_usd' => $discountUsd,
                'phone' => $data['phone'],
                'call_preference' => $data['call_preference'] ?? 'call_me',
                // Keep order creation working even if production schema has email as NOT NULL.
                'email' => $data['email'] ?? '',
            ]);

            if ($promo) {

                $promo->increment('used_count');

                PromoCodeUsage::create([
                    'promo_code_id' => $promo->id,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                ]);
            }

            foreach ($itemsData as $item) {

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
                $this->buildTelegramMessage($order)
            );

            if (! $sent) {
                Log::warning('Order created but Telegram notification failed', [
                    'order_id' => $order->id,
                ]);
            }

            return $order;
        });
    }

    protected function buildTelegramMessage(Order $order): string
    {
        if (! $order->relationLoaded('items')) {
            $order->load('items');
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
        }

        return $message;
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
