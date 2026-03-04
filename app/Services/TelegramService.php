<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected string $token;
    protected string $chatId;

    public function __construct()
    {
        $settings = Setting::getSettings();

        $this->token = $settings->telegram_bot_token;
        $this->chatId = $settings->telegram_chat_id;
    }

    public function send(string $message): void
    {
        if (!$this->token || !$this->chatId) {
            return;
        }

        Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }
}
