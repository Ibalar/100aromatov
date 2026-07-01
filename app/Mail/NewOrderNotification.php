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

class NewOrderNotification extends Mailable
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {
        Log::debug('NewOrderNotification: building mail', [
            'order_id' => $this->order->id,
        ]);
    }

    public function envelope(): Envelope
    {
        $adminEmail = Setting::getSettings()->email;

        return new Envelope(
            subject: __('Новый заказ #:id', ['id' => $this->order->id]),
            to: $adminEmail,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-notification',
            with: [
                'order' => $this->order,
                'settings' => Setting::getSettings(),
            ],
        );
    }
}
