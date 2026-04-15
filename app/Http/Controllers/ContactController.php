<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $settings = Setting::getSettings();
        $email = trim((string) ($settings->email ?? ''));

        if ($email === '' && preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', (string) ($settings->requisites ?? ''), $matches)) {
            $email = $matches[0];
        }

        return view('contact', [
            'contactEmail' => $email !== '' ? $email : null,
        ]);
    }
}
