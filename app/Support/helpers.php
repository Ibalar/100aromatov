<?php

if (! function_exists('localizedField')) {
    function localizedField($model, string $field): mixed
    {
        if (! $model) {
            return null;
        }

        $locale = app()->getLocale();
        $localizedField = sprintf('%s_%s', $field, $locale);
        $value = $model->getAttribute($localizedField);

        if (! blank($value)) {
            return $value;
        }

        $baseValue = $model->getAttribute($field);

        if (! blank($baseValue)) {
            return $baseValue;
        }

        $fallbackLocale = $locale === 'by' ? 'ru' : 'by';
        $fallbackField = sprintf('%s_%s', $field, $fallbackLocale);
        $fallbackValue = $model->getAttribute($fallbackField);

        if (! blank($fallbackValue)) {
            return $fallbackValue;
        }

        return $baseValue;
    }
}

if (! function_exists('formatPriceByn')) {
    function formatPriceByn(float $usdPrice, ?float $usdRate = null): string
    {
        if ($usdRate === null) {
            $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;
        }

        $bynPrice = round($usdPrice * $usdRate, 2);

        return number_format($bynPrice, 2, ',', ' ') . ' BYN';
    }
}

if (! function_exists('formatPriceUsd')) {
    function formatPriceUsd(float $usdPrice): string
    {
        return '$' . number_format($usdPrice, 2, '.', '');
    }
}

if (! function_exists('getPriceInByn')) {
    function getPriceInByn(float $usdPrice, ?float $usdRate = null): float
    {
        if ($usdRate === null) {
            $usdRate = \App\Models\Setting::getSettings()->usd_rate ?? 1;
        }

        return round($usdPrice * $usdRate, 2);
    }
}

if (! function_exists('phoneHref')) {
    function phoneHref(?string $phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return 'tel:';
        }

        $normalized = preg_replace('/(?!^\+)[^\d]/', '', $phone) ?? $phone;

        return 'tel:' . $normalized;
    }
}

if (! function_exists('settingPhoneIconUrl')) {
    function settingPhoneIconUrl(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

if (! function_exists('normalizeBelarusPhone')) {
    function normalizeBelarusPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '80') && strlen($digits) === 11) {
            $digits = '375' . substr($digits, 2);
        } elseif (strlen($digits) === 9) {
            $digits = '375' . $digits;
        }

        return $digits;
    }
}

if (! function_exists('isValidBelarusMobilePhone')) {
    function isValidBelarusMobilePhone(?string $phone): bool
    {
        $normalized = normalizeBelarusPhone($phone);

        if (! $normalized) {
            return false;
        }

        return (bool) preg_match('/^375(25|29|33|44)\d{7}$/', $normalized);
    }
}

if (! function_exists('formatBelarusMobilePhone')) {
    function formatBelarusMobilePhone(?string $phone): ?string
    {
        $normalized = normalizeBelarusPhone($phone);

        if (! $normalized || ! isValidBelarusMobilePhone($normalized)) {
            return null;
        }

        $operator = substr($normalized, 3, 2);
        $part1 = substr($normalized, 5, 3);
        $part2 = substr($normalized, 8, 2);
        $part3 = substr($normalized, 10, 2);

        return "+375 ({$operator}) {$part1}-{$part2}-{$part3}";
    }
}
