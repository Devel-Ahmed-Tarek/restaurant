<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'de'];

    public function handle(Request $request, Closure $next, ...$locales): Response
    {
        $locale = $request->route('locale', session('locale', config('app.locale')));

        if ($locale && in_array($locale, $this->supported, true)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
