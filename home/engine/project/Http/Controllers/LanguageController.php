<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, string $lang): RedirectResponse
    {
        $languageService = app(LanguageService::class);
        
        // Validate locale
        if (!$languageService->isValidLocale($lang)) {
            $lang = $languageService->getDefaultLocale();
        }
        
        // Set the locale
        $languageService->setLocale($lang);
        
        // Get the previous URL or default to home
        $redirectUrl = $request->server('HTTP_REFERER') 
            ? $request->server('HTTP_REFERER') 
            : route('home');
        
        // Remove lang parameter from URL if present and add correct one
        $redirectUrl = preg_replace('/([?&])lang=[^&]*&?/', '$1', $redirectUrl);
        $redirectUrl = rtrim($redirectUrl, '?');
        
        // Add the new lang parameter
        $separator = str_contains($redirectUrl, '?') ? '&' : '?';
        $redirectUrl = $redirectUrl . $separator . 'lang=' . $lang;
        
        return redirect()->to($redirectUrl);
    }
}
