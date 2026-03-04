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

                $priceUsd = $variant->sale_price_usd ?? $variant->price_usd;

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
                'status' => 'new',
                'total_usd' => $totalUsdFinal,
                'total_byn' => $totalByn,
                'promo_code' => $promo?->code,
                'discount_usd' => $discountUsd,
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
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
                    'qty' => $item['qty'],
                    'price_byn_snapshot' => $priceByn,
                ]);
            }

            app(TelegramService::class)->send(
                $this->buildTelegramMessage($order)
            );

            return $order;
        });
    }

    protected function buildTelegramMessage(Order $order): string
    {
        $message = "<b>Новый заказ #{$order->id}</b>\n";
        $message .= "Телефон: {$order->phone}\n";
        $message .= "Сумма: {$order->total_byn} BYN\n\n";

        foreach ($order->items as $item) {
            $message .= "{$item->name_snapshot} × {$item->qty}\n";
        }

        return $message;
    }
}
