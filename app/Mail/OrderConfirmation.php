<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Log;

class OrderConfirmation extends Mailable
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {
        Log::debug('OrderConfirmation: building mail', [
            'order_id' => $this->order->id,
            'to' => $this->order->email,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Заказ №:id оформлен', ['id' => $this->order->id]),
            to: $this->order->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
                'settings' => Setting::getSettings(),
            ],
        );
    }
}
