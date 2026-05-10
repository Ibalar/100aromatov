<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;

class Setting extends Model
{
    protected $fillable = [
        'usd_rate',
        'telegram_bot_token',
        'telegram_chat_id',
        'telegram_url',
        'viber_url',
        'whatsapp_url',
        'email',
        'phones',
        'address',
        'address_map_url',
        'instagram_url',
        'google_reviews_url',
        'yandex_reviews_url',
        'yandex_rating',
        'yandex_reviews_count',
        'requisites',
        'metrics_head_code',
        'metrics_body_start_code',
        'metrics_body_end_code',
        'infinite_slide_items',
    ];

    protected $casts = [
        'usd_rate' => 'decimal:4',
        'yandex_rating' => 'decimal:2',
        'yandex_reviews_count' => 'integer',
        'phones' => 'array',
        'infinite_slide_items' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Singleton accessor
    |--------------------------------------------------------------------------
    */

    protected static ?self $instance = null;

    public static function getSettings(): self
    {
        if (static::$instance === null) {
            static::$instance = static::firstOrCreate([]);
        }

        return static::$instance;
    }

    public function setPhonesAttribute($value): void
    {
        $phones = collect(is_iterable($value) ? $value : [])
            ->map(function ($phone) {
                $phone = is_array($phone) ? $phone : [];

                return [
                    'label' => filled($phone['label'] ?? null) ? trim((string) $phone['label']) : null,
                    'number' => filled($phone['number'] ?? null) ? trim((string) $phone['number']) : null,
                    'icon' => $this->normalizePhoneIcon($phone['icon'] ?? null),
                ];
            })
            ->filter(fn (array $phone): bool => filled($phone['number']))
            ->values()
            ->all();

        $this->attributes['phones'] = $phones === [] ? null : json_encode($phones, JSON_UNESCAPED_UNICODE);
    }

    public function getPhonesAttribute($value): array
    {
        if (blank($value)) {
            return [];
        }

        $phones = is_array($value) ? $value : json_decode((string) $value, true);

        if (! is_array($phones)) {
            return [];
        }

        return collect($phones)
            ->filter(fn ($phone): bool => is_array($phone) && filled($phone['number'] ?? null))
            ->map(fn (array $phone): array => [
                'label' => $phone['label'] ?? null,
                'number' => $phone['number'] ?? null,
                'icon' => $phone['icon'] ?? null,
            ])
            ->values()
            ->all();
    }

    public function setInfiniteSlideItemsAttribute($value): void
    {
        $items = collect(is_iterable($value) ? $value : [])
            ->map(function ($item) {
                $item = is_array($item) ? $item : [];

                $icon = trim((string) ($item['icon'] ?? 'icon-Lightning-1'));
                $textRu = trim((string) ($item['text_ru'] ?? ''));
                $textBy = trim((string) ($item['text_by'] ?? ''));

                return [
                    'icon' => $icon !== '' ? $icon : 'icon-Lightning-1',
                    'text_ru' => $textRu,
                    'text_by' => $textBy,
                ];
            })
            ->filter(fn (array $item): bool => filled($item['text_ru']) || filled($item['text_by']))
            ->values()
            ->all();

        $this->attributes['infinite_slide_items'] = $items === [] ? null : json_encode($items, JSON_UNESCAPED_UNICODE);
    }

    public function setMetricsHeadCodeAttribute($value): void
    {
        $this->attributes['metrics_head_code'] = $this->normalizeMetricsCode($value);
    }

    public function getMetricsHeadCodeAttribute($value): ?string
    {
        return $this->normalizeMetricsCode($value);
    }

    public function setMetricsBodyStartCodeAttribute($value): void
    {
        $this->attributes['metrics_body_start_code'] = $this->normalizeMetricsCode($value);
    }

    public function getMetricsBodyStartCodeAttribute($value): ?string
    {
        return $this->normalizeMetricsCode($value);
    }

    public function setMetricsBodyEndCodeAttribute($value): void
    {
        $this->attributes['metrics_body_end_code'] = $this->normalizeMetricsCode($value);
    }

    public function getMetricsBodyEndCodeAttribute($value): ?string
    {
        return $this->normalizeMetricsCode($value);
    }

    public function getInfiniteSlideItemsAttribute($value): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_array($value) ? $value : json_decode((string) $value, true);

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                $icon = trim((string) ($item['icon'] ?? 'icon-Lightning-1'));
                $textRu = trim((string) ($item['text_ru'] ?? ''));
                $textBy = trim((string) ($item['text_by'] ?? ''));

                return [
                    'icon' => $icon !== '' ? $icon : 'icon-Lightning-1',
                    'text_ru' => $textRu,
                    'text_by' => $textBy,
                ];
            })
            ->filter(fn (array $item): bool => filled($item['text_ru']) || filled($item['text_by']))
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function convertUsdToByn(float $usd): float
    {
        return round($usd * $this->usd_rate, 2);
    }

    private function normalizePhoneIcon(mixed $icon): ?string
    {
        if ($icon instanceof UploadedFile) {
            return $icon->store('settings/phones', 'public');
        }

        if ($icon instanceof UploadedFileInterface) {
            $clientFilename = (string) $icon->getClientFilename();
            $extension = pathinfo($clientFilename, PATHINFO_EXTENSION);
            $extension = $extension !== '' ? strtolower($extension) : $this->guessExtensionFromMimeType($icon->getClientMediaType());
            $path = 'settings/phones/' . Str::uuid() . ($extension ? '.' . $extension : '');

            Storage::disk('public')->put($path, $icon->getStream()->getContents());

            return $path;
        }

        if (is_array($icon)) {
            foreach (['hidden_icon', 'icon', 'path', 'value'] as $key) {
                if (filled($icon[$key] ?? null)) {
                    return ltrim((string) $icon[$key], '/');
                }
            }

            return null;
        }

        if (! filled($icon)) {
            return null;
        }

        return ltrim((string) $icon, '/');
    }

    private function guessExtensionFromMimeType(?string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/avif' => 'avif',
            default => '',
        };
    }

    private function normalizeMetricsCode(mixed $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        return html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
