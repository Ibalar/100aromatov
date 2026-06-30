<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>{{ __('Заказ №:id оформлен', ['id' => $order->id]) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #1a1a1a; font-size: 24px; margin-bottom: 20px;">
        {{ __('Заказ №:id оформлен', ['id' => $order->id]) }}
    </h1>

    <p>{{ __('Спасибо! Ваш заказ принят в обработку.') }}</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr>
            <td style="padding: 8px 0; color: #666; width: 140px;">{{ __('Статус') }}</td>
            <td style="padding: 8px 0;"><strong>{{ __('Новый') }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Телефон') }}</td>
            <td style="padding: 8px 0;">{{ $order->phone }}</td>
        </tr>
        @if ($order->call_preference === 'no_call')
        <tr>
            <td style="padding: 8px 0; color: #666;">{{ __('Перезвон') }}</td>
            <td style="padding: 8px 0;">{{ __('Перезванивать не нужно') }}</td>
        </tr>
        @endif
        @if ($order->email)
        <tr>
            <td style="padding: 8px 0; color: #666;">Email</td>
            <td style="padding: 8px 0;">{{ $order->email }}</td>
        </tr>
        @endif
    </table>

    @if ($order->items->isNotEmpty())
    <h2 style="font-size: 18px; color: #1a1a1a; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        {{ __('Состав заказа') }}
    </h2>
    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
        @foreach ($order->items as $item)
        <tr style="border-bottom: 1px solid #f0f0f0;">
            <td style="padding: 8px 0;">
                {{ $item->name_snapshot }}
                @if ($item->volume_ml_snapshot)
                    ({{ $item->volume_ml_snapshot }} ml)
                @endif
                <span style="color: #999;">x{{ $item->qty }}</span>
            </td>
            <td style="padding: 8px 0; text-align: right;">
                {{ number_format((float) $item->price_byn_snapshot * $item->qty, 2, ',', ' ') }} BYN
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
        <table style="width: 100%;">
            <tr>
                <td style="font-size: 18px; font-weight: bold;">{{ __('Итого') }}</td>
                <td style="font-size: 18px; font-weight: bold; text-align: right;">
                    {{ number_format((float) $order->total_byn, 2, ',', ' ') }} BYN
                </td>
            </tr>
            @if ($order->discount_usd > 0)
            <tr>
                <td style="color: #16a34a;">{{ __('Скидка') }}</td>
                <td style="color: #16a34a; text-align: right;">
                    -{{ number_format($settings->convertUsdToByn((float) $order->discount_usd), 2, ',', ' ') }} BYN
                </td>
            </tr>
            @endif
        </table>
    </div>

    <p style="margin-top: 30px; padding: 15px; background: #f8f8f8; border-radius: 6px; color: #666; font-size: 14px;">
        {{ __('Мы свяжемся с вами для подтверждения заказа.') }}<br>
        <a href="{{ config('app.url') }}" style="color: #1a1a1a;">{{ config('app.name') }}</a>
    </p>
</body>
</html>
