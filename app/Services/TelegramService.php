<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $token;
    protected ?string $chatId;

    public function __construct()
    {
        $settings = Setting::getSettings();

        $this->token = $settings->telegram_bot_token;
        $this->chatId = $settings->telegram_chat_id;
    }

    public function send(string $message): bool
    {
        if (! filled($this->token) || ! filled($this->chatId)) {
            Log::warning('Telegram settings are not configured');
            return false;
        }

        try {
            $response = $this->request('sendMessage', [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->ok()) {
                Log::error('Telegram send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram send exception', [
                'message' => $this->sanitizeTelegramErrorMessage($e->getMessage()),
            ]);
            return false;
        }
    }

    private function request(string $method, array $payload)
    {
        return Http::withOptions([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ])
            ->connectTimeout(15)
            ->timeout(45)
            ->retry(4, 1000)
            ->post("https://api.telegram.org/bot{$this->token}/{$method}", $payload);
    }

    private function sanitizeTelegramErrorMessage(string $message): string
    {
        return (string) preg_replace(
            '/bot\d+:[A-Za-z0-9_-]+/u',
            'bot***',
            $message
        );
    }
}
