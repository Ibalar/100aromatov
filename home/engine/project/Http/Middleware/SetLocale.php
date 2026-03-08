<?php

namespace App\Http\Middleware;

use App\Services\LanguageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $languageService = app(LanguageService::class);
        
        // Detect and set locale
        $locale = $languageService->detectLocale($request);
        $languageService->setLocale($locale);
        
        // Share locale with all views
        view()->share('locale', $locale);
        view()->share('currentLocale', $locale);
        
        return $next($request);
    }
}
