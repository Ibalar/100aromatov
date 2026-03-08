<?php

namespace App\Services;

use Illuminate\Http\Request;

class LanguageService
{
    public const DEFAULT_LOCALE = 'ru';
    public const SUPPORTED_LOCALES = ['ru', 'by'];

    public function getLocale(?Request $request = null): string
    {
        $request = $request ?? request();
        $locale = $this->detectLocale($request);

        return $locale ?? self::DEFAULT_LOCALE;
    }

    public function detectLocale(Request $request): ?string
    {
        $lang = $request->query('lang');
        if ($this->isSupported($lang)) {
            return $lang;
        }

        $sessionLocale = $request->session()->get('locale');
        if ($this->isSupported($sessionLocale)) {
            return $sessionLocale;
        }

        $cookieLocale = $request->cookie('locale');
        if ($this->isSupported($cookieLocale)) {
            return $cookieLocale;
        }

        return null;
    }

    public function setLocale(string $locale, ?Request $request = null): string
    {
        $request = $request ?? request();
        $locale = $this->isSupported($locale) ? $locale : self::DEFAULT_LOCALE;

        $request->session()->put('locale', $locale);
        cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));

        return $locale;
    }

    private function isSupported(?string $locale): bool
    {
        return $locale !== null && in_array($locale, self::SUPPORTED_LOCALES, true);
    }
}
