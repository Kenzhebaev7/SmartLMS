<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (!in_array($locale, \App\Http\Middleware\SetLocale::LOCALES, true)) {
            return redirect()->back();
        }
        session()->put('locale', $locale);
        return redirect()->back()->cookie('locale', $locale, 60 * 24 * 365); // 1 year, чтобы после выхода переводы работали
    }
}
