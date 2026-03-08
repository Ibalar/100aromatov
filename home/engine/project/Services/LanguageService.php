<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LanguageService
{
    /**
     * Available locales
     */
    protected array $locales = ['ru', 'by'];

    /**
     * Default locale
     */
    protected string $defaultLocale = 'ru';

    /**
     * Cookie name for storing language preference
     */
    protected string $cookieName = 'app_locale';

    /**
     * Session key for storing language
     */
    protected string $sessionKey = 'locale';

    /**
     * Get current locale
     */
    public function getLocale(): string
    {
        return Session::get($this->sessionKey, $this->defaultLocale);
    }

    /**
     * Detect locale from various sources
     * Priority: 1. URL parameter, 2. Session, 3. Cookie
     */
    public function detectLocale(Request $request): string
    {
        // 1. Check URL parameter
        if ($request->has('lang') && $this->isValidLocale($request->get('lang'))) {
            return $request->get('lang');
        }

        // 2. Check session
        if (Session::has($this->sessionKey) && $this->isValidLocale(Session::get($this->sessionKey))) {
            return Session::get($this->sessionKey);
        }

        // 3. Check cookie
        $cookieLocale = $request->cookie($this->cookieName);
        if ($cookieLocale && $this->isValidLocale($cookieLocale)) {
            return $cookieLocale;
        }

        // Default to Russian
        return $this->defaultLocale;
    }

    /**
     * Set locale in session and cookie
     */
    public function setLocale(string $locale): void
    {
        if (!$this->isValidLocale($locale)) {
            $locale = $this->defaultLocale;
        }

        Session::put($this->sessionKey, $locale);
        
        // Set cookie for 1 year
        Cookie::queue($this->cookieName, $locale, 60 * 24 * 365);
        
        // Also set Laravel app locale
        app()->setLocale($locale);
    }

    /**
     * Check if locale is valid
     */
    public function isValidLocale(string $locale): bool
    {
        return in_array($locale, $this->locales, true);
    }

    /**
     * Get all available locales
     */
    public function getAvailableLocales(): array
    {
        return $this->locales;
    }

    /**
     * Get default locale
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Get current locale (alias for getLocale)
     */
    public function currentLocale(): string
    {
        return $this->getLocale();
    }

    /**
     * Check if current locale is Russian
     */
    public function isRussian(): bool
    {
        return $this->getLocale() === 'ru';
    }

    /**
     * Check if current locale is Belarusian
     */
    public function isBelarusian(): bool
    {
        return $this->getLocale() === 'by';
    }
}
