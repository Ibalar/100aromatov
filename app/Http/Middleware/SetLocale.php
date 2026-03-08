<?php

namespace App\Http\Middleware;

use App\Services\LanguageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private readonly LanguageService $languageService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang')) {
            $this->languageService->setLocale($request->query('lang'), $request);
        }

        $locale = $this->languageService->getLocale($request);
        app()->setLocale($locale);

        return $next($request);
    }
}
