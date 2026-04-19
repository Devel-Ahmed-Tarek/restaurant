<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('admin_locale', 'en');
        if (! in_array($locale, ['en', 'de'], true)) {
            $locale = 'en';
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
