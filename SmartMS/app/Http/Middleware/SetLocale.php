<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const LOCALES = ['kk', 'ru'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', $request->cookie('locale', config('app.locale')));
        if (!in_array($locale, self::LOCALES, true)) {
            $locale = 'kk';
        }
        App::setLocale($locale);

        $response = $next($request);

        // Чтобы после выхода и обновления страницы переводы работали — сохраняем локаль в cookie
        if (!$request->cookie('locale')) {
            $response->cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false);
        }

        return $response;
    }
}
