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
