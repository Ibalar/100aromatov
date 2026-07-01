<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ __('Новый заказ #:id', ['id' => $order->id]) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #1a1a1a; font-size: 24px; margin-bottom: 20px;">
        {{ __('Новый заказ #:id', ['id' => $order->id]) }}
    </h1>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 8px 0; color: #666; width: 140px;">{{ __('Телефон') }}</td>
            <td style="padding: 8px 0;"><strong>{{ $order->phone }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Перезвон') }}</td>
            <td style="padding: 8px 0;">
                {{ $order->call_preference === 'no_call' ? __('Перезванивать не нужно') : __('Перезвоните') }}
            </td>
        </tr>
        @if ($order->email)
        <tr>
            <td style="padding: 8px 0; color: #666;">Email</td>
            <td style="padding: 8px 0;">{{ $order->email }}</td>
        </tr>
        @endif
        @if ($order->promo_code)
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Промокод') }}</td>
            <td style="padding: 8px 0;">{{ $order->promo_code }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Сумма') }}</td>
            <td style="padding: 8px 0;"><strong>{{ number_format((float) $order->total_byn, 2, ',', ' ') }} BYN</strong></td>
        </tr>
        @if ($order->discount_usd > 0)
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Скидка') }}</td>
            <td style="padding: 8px 0;">
                {{ number_format($settings->convertUsdToByn((float) $order->discount_usd), 2, ',', ' ') }} BYN
            </td>
        </tr>
        @endif
    </table>

    @if ($order->items->isNotEmpty())
    <h2 style="font-size: 18px; color: #1a1a1a; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        {{ __('Позиции') }}
    </h2>
    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
        @foreach ($order->items as $item)
        <tr style="border-bottom: 1px solid #f0f0f0;">
            <td style="padding: 8px 0;">
                {{ $item->name_snapshot }}
                @if ($item->volume_ml_snapshot)
                    ({{ $item->volume_ml_snapshot }} ml)
                @endif
                @if ($item->sku_snapshot)
                    <br><span style="font-size: 12px; color: #999;">SKU: {{ $item->sku_snapshot }}</span>
                @endif
            </td>
            <td style="padding: 8px 0; text-align: right; white-space: nowrap;">
                {{ $item->qty }} x {{ number_format((float) $item->price_byn_snapshot, 2, ',', ' ') }} BYN
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <p style="margin-top: 30px; padding: 15px; background: #f0f7ff; border-radius: 6px; color: #1a56db; font-size: 14px;">
        <a href="{{ config('app.url') }}/admin/resource/order-resource/detail-page/{{ $order->id }}" style="color: #1a56db;">
            {{ __('Открыть заказ в админ-панели') }}
        </a>
    </p>
</body>
</html>
