<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(string $lang, Request $request, LanguageService $languageService): RedirectResponse
    {
        $languageService->setLocale($lang, $request);

        return redirect()->back();
    }
}
